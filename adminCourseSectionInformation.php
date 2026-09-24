<?php 
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start or resume a session
session_start();

// Check if the user is logged in and has Admin role, if not, redirect to login page
//if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
//    header("Location: login.php");
//    exit();
//}

require(__DIR__ . "/connect.php");

    $_SESSION['InstructorName'] = $_GET['instructorName'] ?? 'Unknown Instructor';
    $instructorName = $_SESSION['InstructorName'] ?? 'Unknown Instructor';



if (isset($_GET['id'])) {
    $_SESSION['CourseID'] = $_GET['id'];
}


$sectionId = $_SESSION['SectionID'] ?? null;
$courseCode = $_SESSION['CourseCode'] ?? 'Unknown Course';
$courseName = $_SESSION['CourseName'] ?? 'Unknown Course';
$courseId = $_SESSION['CourseID'] ? $_GET['courseID'] : null;

$sectionId = $_GET['sectionId'] ?? null;

// Retrieve students enrolled in this section
$students = [];
if ($sectionId) {
    $stmt_students = $dbconnect->prepare("
        SELECT s.StudentID, s.FName, s.LName
        FROM Students s
        JOIN Enrollment e ON s.StudentID = e.StudentID
        WHERE e.SectionID = :sectionId
    ");
    $stmt_students->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
    $stmt_students->execute();
    $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'removeStudents') {
    $sectionId = $_POST['sectionId'];
    $studentIds = json_decode($_POST['studentIds']);

    try {
        $dbconnect->beginTransaction();

        $stmt_unenroll = $dbconnect->prepare("DELETE FROM Enrollment WHERE SectionID = :sectionId AND StudentID = :studentId");
        foreach ($studentIds as $studentId) {
            $stmt_unenroll->execute([':sectionId' => $sectionId, ':studentId' => $studentId]);
        }

        $dbconnect->commit();
        echo json_encode(["success" => true]);
        exit;
    } catch (PDOException $e) {
        $dbconnect->rollBack();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
        exit;
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
        .sidenav a.home { /*"home" refers to the class or current page we are on since we want to highlight that*/
            background-color: #e3eef4;
            color: #002F6C;
            border-radius: 2px;
            border-left: 4px solid #002F6C;
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
.main-content {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            padding-left: 250px; /* Offset the padding by the width of the sidebar */
            padding-top: 70px;
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
        .information {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 20px;
        }
        .students-list {
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
            padding: 15px;
               
            list-style-type: none;
        }
         .students-list-header {
    		display: flex;
    		justify-content: space-between;
    		align-items: center;
    		padding-bottom: 10px;
                
    		  align-items: center;
		}
        .students-list h3{
            margin: 0;
        }
        .students-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .students-list li:last-child {
            border-bottom: none;
        }
.student-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .student-item:last-child {
            border-bottom: none;
        }
        .student-info {
            display: flex;
            align-items: center;
        }
        .student-info label {
            margin-left: 10px;
        }
        .remove-student-btn {
            color: #fff;
      width: 150px;
      height: 35px;;
      font-size: 15px;;
      font-family: "Georgia", serif;
      text-align: center;
      background-color: #C8102E; 
     flex-grow: 0; /* Prevents the button from growing */
    border-radius: 10px;
             }
        .remove-student-btn:hover {
            background-color: #a1081e;
        }
.section-home-button {
      color: black;
      width: 150px;
      height: 25px;;
      font-size: 15px;;
      font-family: "Georgia", serif;
      text-align: center;
      background-color: #F8F8F6; 
     flex-grow: 0; /* Prevents the button from growing */
    border-radius: 10px;
      margin-top: 10px;
    }
.section-home-button:hover,
    .upload:focus{
      background-color: #91AEDF;
      cursor: pointer;

    }


     </style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="topbar">
          <a href="allCourses.php" class="breadcrumb-link">All Courses </a><span class="arrow">></span>
	  <a href="courseHomepage.php" class="breadcrumb-link"> Sections </a><span class="arrow">></span>
    <span class="breadcrumb-current">Section Information</span>

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
        <div class="information">
            <h2><?php echo htmlspecialchars($courseCode); ?> <?php echo htmlspecialchars($courseName); ?></h2>
        <p>Course Section: <?php echo htmlspecialchars($sectionId); ?></p>
        <p>Section Instructor: <?php echo htmlspecialchars($instructorName); ?></p>
        <button class="section-home-button" onclick="location.href='adminCourseSectionHome.php?courseID=<?= $courseId ?>&sectionID=<?= $sectionId ?>'">Section Homepage</button>


        </div>
        
         <ul class="students-list">
            <div class="students-list-header">
         <h3>Enrolled Students</h3>
      <button class="remove-student-btn" onclick="removeSelectedStudents('<?php echo htmlspecialchars($sectionId); ?>')">Remove Selected</button>
           </div>
        <ul>
            <?php foreach ($students as $student): ?>
                <li class="student-item" id="student-<?php echo htmlspecialchars($student['StudentID']); ?>">
                    <div class="student-info">
                        <input type="checkbox" id="student-<?php echo htmlspecialchars($student['StudentID']); ?>-checkbox" value="<?php echo htmlspecialchars($student['StudentID']); ?>">
                        <label for="student-<?php echo htmlspecialchars($student['StudentID']); ?>-checkbox">
                            ID: <?php echo htmlspecialchars($student['StudentID']); ?> - <?php echo htmlspecialchars($student['FName']) . ' ' . htmlspecialchars($student['LName']); ?>
                        </label>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>    </div>
</div>
<script>
function removeSelectedStudents(sectionId) {
    let checkedStudents = document.querySelectorAll('.students-list input[type="checkbox"]:checked');
    let studentIds = Array.from(checkedStudents).map(cb => cb.value);

    if (studentIds.length === 0) {
        alert("Please select at least one student to remove.");
        return;
    }

    if (confirm('Are you sure you want to remove the selected students from the section?')) {
        fetch('adminCourseSectionInformation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=removeStudents&sectionId=' + sectionId + '&studentIds=' + JSON.stringify(studentIds)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the student items from the DOM
                checkedStudents.forEach(cb => {
                    let studentItem = cb.closest('.student-item');
                    studentItem.remove();
                });
            } else {
                alert("There was a problem unenrolling the students: " + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
}
</script></body>
</html>