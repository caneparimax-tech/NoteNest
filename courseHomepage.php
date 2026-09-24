<?php 
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Start or resume a session
session_start();

// Check if the user is logged in and has Admin role, if not, redirect to login page
//if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
    //header("Location: login.php");
    //exit();
//}

// Setup database connection
require(__DIR__ . "/connect.php");

if (isset($_GET['id'])) { $_SESSION['CourseID'] = $_GET['id']; }
$courseId = $_SESSION['CourseID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sectionId'])) {
    $sectionId = $_POST['sectionId'];

    // Begin transaction
    $dbconnect->beginTransaction();

    try {
        // Delete notes associated with the section
        $stmt_notes = $dbconnect->prepare("DELETE FROM Notes WHERE SectionID = ?");
        $stmt_notes->execute([$sectionId]);
        
        // Delete enrollments associated with the section
        $stmt_enrollment = $dbconnect->prepare("DELETE FROM Enrollment WHERE SectionID = ?");
        $stmt_enrollment->execute([$sectionId]);
        
        // Delete the section
        $stmt_section = $dbconnect->prepare("DELETE FROM Sections WHERE SectionID = ?");
        $stmt_section->execute([$sectionId]);

        // Commit the transaction
        $dbconnect->commit();

        // Return success message
        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        // If an error occurs, rollback and return an error message
        $dbconnect->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}


// Fetch the course details
try {
    $stmt_course = $dbconnect->prepare("SELECT * FROM Courses WHERE CourseID = :courseId");
    $stmt_course->bindParam(':courseId', $courseId, PDO::PARAM_INT);
    $stmt_course->execute();
    $course = $stmt_course->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        header("Location: error.php?message=Course not found");
        exit();
    }
} catch (PDOException $e) {
    header("Location: error.php?message=Database error: " . $e->getMessage());
    exit();
}

// Fetch the sections of the course
try {
    $stmt_sections = $dbconnect->prepare("SELECT * FROM Sections WHERE CourseID = :courseId");
    $stmt_sections->bindParam(':courseId', $courseId, PDO::PARAM_INT);
    $stmt_sections->execute();
    $sections = $stmt_sections->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header("Location: error.php?message=Database error: " . $e->getMessage());
    exit();
}

$_SESSION['CourseCode'] = $course['CourseCode'] ?? 'Unknown Course';
$_SESSION['CourseName'] = $course['CourseName'] ?? 'Unknown Course';



// Fetch all instructors
try {
    $stmt_instructors = $dbconnect->prepare("SELECT * FROM Instructors ORDER BY LName ASC");
    $stmt_instructors->execute();
    $instructors = $stmt_instructors->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header("Location: error.php?message=Database error: " . $e->getMessage());
    exit();
}

// Function to get instructor name by ID
function getInstructorName($instructorId, $dbconnect) {
    $sql = "SELECT FName, LName FROM Instructors WHERE InstructorID = :instructorId";
    $stmt = $dbconnect->prepare($sql);
    $stmt->bindParam(':instructorId', $instructorId, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $instructor = $stmt->fetch(PDO::FETCH_ASSOC);
            return $instructor['FName'] . ' ' . $instructor['LName'];
        } else {
            return "Instructor not found";
        }
    } else {
        $errorInfo = $stmt->errorInfo();
        return "Query failed: " . $errorInfo[2];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <title>Course Homepage</title>
    <style>
        header {
            height: 50px;
            width: 100%;
            background-color: #002E6D;
            color: #fff;
            position: fixed;
            top: 0;
            left:0;
            z-index: 999;
        }
.topbar {
    height: 50px; /* Full-height: remove this if you want "auto" height */
  width: calc(100% - 250px); 
  position: fixed; 
  z-index: 1; /* Stay on top */
  left: 250px;
  right: 0;
  position: fixed;
  background-color: #C8102E; 
  overflow-x: hidden; 
  padding: 0px 20px 0px;
  top: 50px; 
  z-index: 998;
  display: flex; 
  align-items: center;
 justify-content: flex-start;
}
.topbar span {
  font-size: 25px;
  color: #fff;
  display: block;
  padding: 0px 4px 0px 4px; 
  
 white-space: nowrap;
 overflow: visible;

}
        .sidenav {
            height: 100%; /* Full-height: remove this if you want "auto" height */
            width: 250px; /* Set the width of the sidebar */
            position: fixed; /* Fixed Sidebar (stay in place on scroll) */
            z-index: 1; /* Stay on top */
            top: 0; /* Stay at the top */
            left: 0;
            background-color: #c4e0ef; 
            overflow-x: hidden; /* Disable horizontal scroll */
            padding-top: 40px;
        }
        /* The navigation menu links */
        .sidenav a {
            padding: 12px 12px 12px 20px;
            text-decoration: none;
            font-size: 23px;
            color: #C8102E;
            display: block;
            margin: 12px;
        }
        /* When you mouse over the navigation links, change their color */
        .sidenav a:hover {
            color: #002F6C;
            background-color: #e3eef4;
        }
        .sidenav img {
            padding-top: 30px;
            padding-right: 70px;
        }
        .sidenav hr {   
            margin: 20px;
            height: 2px; /* thickness of the line */
            background-color: #002F6C; /* colour of the line */
            border: none;
        }
        .container {
            min-width: 300px;
            width: max(700px, calc(80% - 250px)); /* Subtract the sidebar width */
            max-width: 90%;
            left: 0;
            right: 0;
    	      margin-left: auto; /* Center the container on the remaining space */
   	        margin-right: auto; /* Center the container on the remaining space */
  	        margin-top: 100px; /* More than the height of the top bar to avoid hiding under it */
  	        box-sizing: border-box; /* Includes padding and border in the element's total width and height */
            position: static; /* Position relative for absolute child positioning if needed */
  	        text-align: center;
            align-items: center;
            border-radius: 20px;
            padding: 20px; /* Add padding if needed */  

        }
        .container a:not(.admin-button) {
            padding: 12px 12px 17px 20px;
            padding-top: 20px;
            text-align: left;
            text-decoration: none;
            font-size: 23px;
            color: #C8102E;
            display: block;
            margin: 12px;

        }
        .container a:not(.admin-button):hover {
            color:#002F6C
        }
        .admin-button {
          min-width: 270px;
          width: 300px; /* Same as the section cards */
          padding: 20px; /* Adjust if you have different padding for section cards */
          margin: 20px; /* Same as the section cards */
          border: 1px solid #ccc; /* Same as the section cards */
          border-radius: 10px; /* Same as the section cards */
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25); /* Same as the section cards */
          display: flex; /* To enable margin to center the button */
          justify-content: center; /* Center horizontally */
          align-items: center;
          background-color: #C8102E; /* Button specific color */
          color: white; /* Button specific text color */
          text-align: center; /* Center the text inside the button */
          text-decoration: none; /* Remove underline from the link */
          font-weight: bold; /* If you want the button text to be bold */
          cursor: pointer; /* To indicate the link is clickable */
          box-sizing: border-box;
          width: 270px;
          height: 78px;
          transition: background-color 0.5s ease;
        }
        .admin-button:hover {
            background-color: #002F6C !important;
            color: #fff;
            text-decoration: none;
        }
        .section-cards {
          display: flex;
          flex-wrap: wrap;
          justify-content: center; /* This will center the cards within the .section-cards container */
          align-items: flex-start; /* Align items to the start of the cross axis */
          margin: 0 auto; /* This will take care of horizontal centering */
          /* Add some padding inside the .section-cards container */
        }
        .section-card {
          width: fit-content; /* Adjust card width as needed */
          min-width: 270px !important;
          width: 280px;
          background-color: #fff;
          border: 1px solid black;
          border-radius: 10px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow effect */
          margin: 20px;
          padding: 10px;
          max-width: 300px;
          display: flex;
          flex-direction: column;
          justify-content: center; /* This centers the content vertically */
          align-items: center;           
          position: relative;
          cursor: pointer;
          }
          .section-card h3 {
              margin-top: 5px; 
              
          }
          .section-name {
              font-size: 16px;
              font-weight: bold;
              text-align: left;
              color: #333;
          }
          .main-content {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            padding-left: 250px; /* Offset the padding by the width of the sidebar */
            padding-top: 70px;
         }
          .no-sections-message {
              text-align: center;
              margin-top: 20px; /* Space below the course title */
              font-size: 1rem; /* Adjust size as needed */
              color: #333; /* Adjust text color as needed */
          }
          .centered-button {
              display: block;
              background-color: #C8102E !important; /* Red background */
              color: #fff; /* White text */
              padding: 20px;
              margin: 20px;
              border: none; /* No border for the button */
              border-radius: 10px;
              box-shadow: none; /* No shadow for the button */
              text-align: center;
              font-weight: bold;
              width: 270px;
          }
          .dropdown {
            position: relative;
            display: inline-block !important;
            width: inherit;
          }
          /* Dropdown Content */
          .dropdown-content {
            position: absolute;
            background-color: white;
            width: inherit;
            z-index: 1;
            top: 100% !important; /* Directly under the section card */
            left: 0; /* Aligned to the left edge of the section card */
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px); /* Start slightly above for the dropdown effect */
            border-radius:0px 0px 10px 10px;
            font-weight: bold;
            text-decoration: underline;
            color:#C8102E;
            border-left: 1px solid black;
            border-right: 1px solid black;

          }
          .dropdown-content:hover {
          transition: background-color 0.3s ease;
          color: white !important;
          background-color: #C8102E;
          font-weight: bold;
          text-decoration: none;
          }
          /* Show the dropdown content on hover */
          .section-card:hover .dropdown-content {
            display: block;
            opacity: 1; /* Make it visible on hover */
            visibility: visible; /* Show it */
            left: 0;
            transform: translateY(10px); /* Move back to its original position */
            position: relative !important;
            transition: opacity 0.3s, visibility 0.3s, transform 0.3s ease-out; 
            transition: background-color 0.3s ease;
          }
          /* Style the remove button inside the dropdown */
          .remove-btn {
            color: white;
            padding-top: 5px !important;
            padding-bottom: 5px !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
            width: 100%;
            text-decoration: none;
            display: block;
            font-size: 0.8rem !important;
            text-align: center!important;
            margin: 0px !important;
          }
          .remove-btn:hover {
          color: #fff;
          transition: background-color 0.3s ease;
          color: #fff !important;
          }
          .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
          }
          .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
            text-align: center;
          }
          .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
          }
          .close:hover,
          .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
          }
          form {
            padding-bottom: 15px;;
          }
          .add-btn {
            margin-top: 10px;
            background-color: #C8102E;
            color: #fff;
            font-family: "Georgia", serif;
            font-size: 12px;
            padding: 10px;
            border-radius: 5px;
          }
          .add-btn:hover,
          .add-btn:focus {
            background-color: #002E6D;
          }
.breadcrumb-link, .breadcrumb-current {
    color: #fff; 
    text-decoration: none; 
    font-size: 25px;
   
}
.breadcrumb-link:hover {
    /*color: #002E6D; */
    color: white;
    text-decoration: underline;
}
.breadcrumb-current {
    font-weight: bold; 
    padding-left: 0 !important;
}
.breadcrumb-link .arrow {
    color: #fff;
    font-size: 30px; 
    
}

</style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="topbar">
          <a href="allCourses.php" class="breadcrumb-link">All Courses </a><span class="arrow">></span>
             <span class="breadcrumb-current"><?php echo htmlspecialchars($course['CourseCode']); ?> Sections</span>
      
    </div>
    <div class="main-content">
        <div class="sidenav">
            <img src="NoteNest.png" alt="Logo" class="logo" width="300">
            <hr>
            <a href="adminUserHomepage.php" class="home"><i class="material-icons">home</i> Home</a>
            <a href="adminProfile.php" class="profile"><i class="material-icons">account_circle</i> Profile</a>
            <a href="logout.php" class="logout"><i class="material-icons">logout</i> Logout</a>
        </div>
       <div class="container">
    <h2><?php echo htmlspecialchars($course['CourseCode']); ?> <?php echo htmlspecialchars($course['CourseName']); ?></h2>
<p class="no-sections-message" style="display: <?= count($sections) === 0 ? 'block' : 'none' ?>;">No course sections found.</p>
    <div class="section-cards">
        
            <?php foreach ($sections as $section):
$instructorName = getInstructorName($section['InstructorID'], $dbconnect); ?>
<div class="section-card" data-section-id="<?php echo htmlspecialchars($section['SectionID']); ?>" onclick="location.href='adminCourseSectionInformation.php?sectionId=<?php echo htmlspecialchars($section['SectionID']); ?>&courseCode=<?php echo urlencode($course['CourseCode']); ?>&courseName=<?php echo urlencode($course['CourseName']); ?>&instructorName=<?php echo urlencode($instructorName); ?>'">
    <h3>Section ID: <?php echo htmlspecialchars($section['SectionID']); ?></h3>
    <p>Instructor: <?php echo htmlspecialchars($instructorName); ?></p>
    <!-- Dropdown Content -->
    <div class="dropdown">
      <div class="dropdown-content">
        <a href="#" class="remove-btn" onclick="event.stopPropagation(); removeSection(<?php echo htmlspecialchars($section['SectionID']); ?>);">Remove</a>
      </div>
    </div>
</div>
<?php endforeach; ?>
   <a href="#" onclick="openModal();" class="admin-button">Add Course Section</a>

        <!-- Add Course Section button -->
        <div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Add Course Section</h2>
    <form id="addSectionForm">
        <label for="instructor-id-modal">Instructor:</label>
        <select name="instructor_id" id="instructor-id-modal">
            <option value="">Select Instructor</option>
            <?php foreach ($instructors as $instructor): ?>
                <option value="<?php echo $instructor['InstructorID']; ?>">
                    <?php echo $instructor['FName'] . ' ' . $instructor['LName']; ?>
                </option>
            <?php endforeach; ?>
        </select><br>
<input type="button" class="add-btn" id="submitAddSection" value="Add">
    </form>
  </div>
</div>
<script>

document.addEventListener('DOMContentLoaded', function() {
    // Delegated event listener for section cards
    document.body.addEventListener('click', function(event) {
        if (event.target.closest('.section-card')) {
            var sectionId = event.target.closest('.section-card').getAttribute('data-section-id');
            if (sectionId) {
                window.location.href = 'adminCourseSectionInformation.php?sectionId=' + encodeURIComponent(sectionId) + '&courseCode=' + encodeURIComponent('<?php echo $course['CourseCode']; ?>') + '&courseName=' + encodeURIComponent('<?php echo $course['CourseName']; ?>') + '&instructorName=' + encodeURIComponent(event.target.closest('.section-card').querySelector('.instructor-name').textContent);
            }
        }
    });

    // Existing code for modal handling
    var modal = document.getElementById("myModal");
    var btn = document.querySelector(".admin-button");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }
    
    span.onclick = function() {
        modal.style.display = "none";
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});

function removeSection(sectionId) {
  if (confirm('Are you sure you want to remove this section?')) {
    fetch('removeSection.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'sectionId=' + sectionId
    })
    .then(response => response.json()) 
    .then(data => {
      if (data.success) {
        var sectionCard = document.querySelector('.section-card[data-section-id="' + sectionId + '"]');
        if (sectionCard) {
          sectionCard.remove(); 
          
          var remainingSections = document.querySelectorAll('.section-card').length;
          var noSectionsMsg = document.querySelector('.no-sections-message');
          if (remainingSections === 0 && noSectionsMsg) {
            noSectionsMsg.style.display = 'block';
          }
        }
      } else {
        alert("There was a problem removing the section: " + data.message);
      }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
  }
}

function openModal() {
  var modal = document.getElementById("myModal") ;
  modal.style.display = "block";
}
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.querySelector(".admin-button");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

// Function to add a new section card to the DOM
function addSectionCard(sectionId, instructorName) {
    var addButton = document.querySelector('.admin-button');
    var sectionCard = document.createElement('div');
    sectionCard.classList.add('section-card');
    sectionCard.setAttribute('data-section-id', sectionId);
    sectionCard.innerHTML = `
        <h3>Section ID: ${sectionId}</h3>
        <p>Instructor: <span class="instructor-name">${instructorName}</span></p>
        <div class="dropdown">
            <div class="dropdown-content">
                <a href="#" class="remove-btn" onclick="event.stopPropagation(); removeSection(${sectionId});">Remove</a>
            </div>
        </div>
    `;
    addButton.parentNode.insertBefore(sectionCard, addButton);
}


document.getElementById("submitAddSection").addEventListener("click", function() {
  var instructorId = document.getElementById("instructor-id-modal").value;
  var courseId = '<?php echo $courseId; ?>';

  var formData = new FormData();
  formData.append('instructor_id', instructorId);

  fetch('addSection.php?id=' + courseId, {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // If the section was successfully added, add it to the page without refreshing
      addSectionCard(data.sectionId, data.instructorName);
      modal.style.display = "none";
    } else {
      // If there was a problem (such as the instructor already being assigned), alert the user
      alert("Failed to add section: " + data.message);
    }
  })
  .catch((error) => {
    console.error('Error:', error);
  });
});
</script></body>
</html>