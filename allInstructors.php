<?php 
    session_start();

    //check if user is logged in and is an admin, if not redirect to login page
    if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
        header("Location: login.php");
        exit();
    }

require(__DIR__ . "/connect.php");

            try {
    $sql = "SELECT InstructorID, FName, LName FROM Instructors ORDER BY LName ASC";
    $stmt = $dbconnect->prepare($sql);
    $stmt->execute();
    $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['instructorId'])) {
    $instructorId = $_POST['instructorId'];

    $dbconnect->beginTransaction();

    try {
        // Fetch all section IDs for the instructor
        $stmt_sections = $dbconnect->prepare("SELECT SectionID FROM Sections WHERE InstructorID = ?");
        $stmt_sections->execute([$instructorId]);
        $sections = $stmt_sections->fetchAll(PDO::FETCH_COLUMN);

        // Remove all related notes for these sections
        foreach ($sections as $sectionId) {
            $stmt_remove_notes = $dbconnect->prepare("DELETE FROM Notes WHERE SectionID = ?");
            $stmt_remove_notes->execute([$sectionId]);
        }

        // Remove all enrollments for these sections
        foreach ($sections as $sectionId) {
            $stmt_unenroll = $dbconnect->prepare("DELETE FROM Enrollment WHERE SectionID = ?");
            $stmt_unenroll->execute([$sectionId]);
        }

        // Remove all sections taught by the instructor
        $stmt_remove_sections = $dbconnect->prepare("DELETE FROM Sections WHERE InstructorID = ?");
        $stmt_remove_sections->execute([$instructorId]);

        // Remove the instructor's entry from the `Instructors` table
        $stmt_remove_instructor = $dbconnect->prepare("DELETE FROM Instructors WHERE InstructorID = ?");
        $stmt_remove_instructor->execute([$instructorId]);

        // Get the UserID for the instructor to remove credentials
        $stmt_userId = $dbconnect->prepare("SELECT UserID FROM Instructors WHERE InstructorID = ?");
        $stmt_userId->execute([$instructorId]);
        $userId = $stmt_userId->fetchColumn();

        // Remove the instructor's user credentials
        if ($userId) {
            $stmt_remove_credentials = $dbconnect->prepare("DELETE FROM UserCredentials WHERE UserID = ?");
            $stmt_remove_credentials->execute([$userId]);
        }

        // If all operations were successful, commit the transaction
        $dbconnect->commit();

        // Echo result or confirmation
        echo json_encode(['success' => true, 'message' => 'Instructor and related data removed successfully']);
    } catch (PDOException $e) {
        // If there is an error, rollback
        $dbconnect->rollBack();
        echo json_encode(['success' => false, 'message' => "Error removing instructor: " . $e->getMessage()]);
        exit;
    }
    exit; // Stop script after handling the POST request
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

  <title>All Instructors</title>
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
.breadcrumb-link, .breadcrumb-current {
color: #fff; 
text-decoration: none; 
font-size: 25px;

}
.breadcrumb-link:hover {
color: #002E6D; 
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
    .welcome {
      max-width: 800px;
      margin: 100px auto;
      text-align: center;
      border: none;
      background-color: #F0F0F0;
      padding-top: 20px;
      margin-right: 285px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .welcome span {
      font-size: 45px;
      text-align: center;
      margin-right: -150px;
      padding-top: 20px;
      margin-top: 10px;
    }
    
    .instructors-container {
           min-width: 300px;
            width: max(700px, calc(80% - 250px)); 
            max-width: 90%;
    	      margin-left: auto; 
   	        margin-right: auto;
  	        margin-top: 40px; 
  	        box-sizing: border-box; 
            position: static;
  	        text-align: center;
            align-items: center;
            border-radius: 20px;
            padding: 20px;  

}
        .instructors-container a:not(.admin-button) {
            padding: 12px 12px 17px 20px;
            padding-top: 20px;
            text-align: left;
            text-decoration: none;
            font-size: 23px;
            color: #C8102E;
            display: block;
            margin: 12px;

        }
        .instructors-container a:not(.admin-button):hover {
            color:#002F6C

        }
.instructors-container h2 {
  
  font-size: 1.5em; 
  color: black; 
  margin-bottom: 20px;
  margin-top: 10px;
  text-align: center; 
}



     .instructor-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center; 
    align-items: flex-start; 
    margin: 0 auto; 
     

        }
        .instructor-card {
          //width: ;
           min-width: 270px !important;
          width: 280px;
          min-height: 35px;
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

.instructor-card h3 {
    margin: 5px;
    min-height: 20px;
    display: flex;
    align-items: center;
}
.instructor-card p {
    min-height: 35px;
    display: flex;
    align-items: center;
}

.instructor-card:hover .dropdown-content {
  opacity: 1;
  visibility: visible;
  transform: translateY(10px);
position: relative !important;
  transition: background-color 0.5s ease
}
    .instructor-name {
        font-size: 16px;
        font-weight: bold;
        text-align: left;
        color: #333;
  text-decoration: none;

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




.main-content {

    display: flex;
    flex-direction: row;
    align-items: flex-start;
    padding-left: 250px; /* Offset the padding by the width of the sidebar */
    padding-top: 70px;
         }


</style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="topbar">
        <span><a href="allInstructors.php" style="color: #fff; text-decoration: none; font-size: 25px;">All Instructors</a></span>
    </div>
    <div class="main-content">
        <div class="sidenav">
            <img src="NoteNest.png" alt="Logo" class="logo" width="300">
            <hr>
            <a href="adminUserHomepage.php" class="home"><i class="material-icons">home</i> Home</a>
            <a href="adminProfile.php" class="profile"><i class="material-icons">account_circle</i> Profile</a>
            <a href="logout.php" class="logout"><i class="material-icons">logout</i> Logout</a>
        </div>
        <div class="instructors-container">
    <h2>All Instructors</h2>
    <div class="instructor-cards">
        <?php foreach ($instructors as $instructor): ?>
            <div class="instructor-card" data-instructor-id="<?php echo htmlspecialchars($instructor['InstructorID']); ?>">
                <!-- Display the full name of the instructor -->
                <h3><?php echo htmlspecialchars($instructor['FName'] . ' ' . $instructor['LName']); ?></h3>
                <!-- Display the instructor ID below the name -->
                <p>Instructor ID: <?php echo htmlspecialchars($instructor['InstructorID']); ?></p>
                <div class="dropdown">
                    <div class="dropdown-content">
                        <a href="#" class="remove-btn">Remove</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('.instructor-card').forEach(card => {
        card.addEventListener('click', function(event) {
            var instructorId = this.dataset.instructorId;
            if (event.target.classList.contains('remove-btn')) {
                event.preventDefault();
                event.stopPropagation();
                removeInstructor(instructorId);
            } else {
                //If we make an instructorInformation page, it would go here:
                //window.location.href = 'instructorInformation.php?id=' + instructorId;
            }
        });
    });
});

   function removeInstructor(instructorId) {
    if (confirm('Are you sure you want to remove this instructor?')) {
        fetch(window.location.href, { // Post to the current file
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'instructorId=' + instructorId
        })
        .then(response => response.json()) 
        .then(data => {
            if (data.success) {
                var instructorCard = document.querySelector('.instructor-card[data-instructor-id="' + instructorId + '"]');
                if (instructorCard) {
                    instructorCard.remove(); 
                }
            } else {
                alert("There was a problem removing the instructor: " + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
    return false; // Prevent default link behavior
}
    </script>
</body>
</html>