<?php 
session_start();

// Check if the user is logged in as an instructor; if not, redirect to the login page
if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Instructor') {
    header("Location: login.php");
    exit();
}

// Establish a new PDO connection
$pdo = new PDO('mysql:host=localhost;dbname=mjcanepa', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Validate the presence of courseID and sectionID in the query string
if (!isset($_GET['sectionID'])) {
    header("Location: error.php?message=sectionID not provided");
    exit();
}

if (!isset($_GET['courseID'])) {
    header("Location: error.php?message=courseID not provided");
    exit();
}

// Retrieve courseID and sectionID from the query string
$courseID = $_GET['courseID'];
$sectionID = $_GET['sectionID'];

// Initialize variables
$courseDetails = [];
$sectionName = '';
$instructorName = 'Unknown';

// Fetch course details
try {
    $stmt = $pdo->prepare("SELECT CourseName, InstructorID FROM Courses WHERE CourseID = ?");
    $stmt->execute([$courseID]);
    $courseDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$courseDetails) {
        throw new Exception("Course not found");
    }
} catch (Exception $e) {
    header("Location: error.php?course-details-message=" . urlencode($e->getMessage()));
    exit();
}

// Fetch section details
try {
    $stmt = $pdo->prepare("SELECT SectionName FROM Sections WHERE SectionID = ?");
    $stmt->execute([$sectionID]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$section) {
        throw new Exception("Section not found");
    }
    $sectionName = $section['SectionName'];
} catch (Exception $e) {
    header("Location: error.php?section-details-message=" . urlencode($e->getMessage()));
    exit();
}

// Fetch instructor details
try {
    $stmt = $pdo->prepare("SELECT FName, LName FROM Instructors WHERE InstructorID = ?");
    $stmt->execute([$courseDetails['InstructorID']]);
    $instructor = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($instructor) {
        $instructorName = $instructor['FName'] . ' ' . $instructor['LName'];
    }
} catch (Exception $e) {
    // If the instructor details are not found, we continue without throwing an error,
    // because the instructor name is not critical for the page to function
}

// Close the database connection
$pdo = null;
?>

// HTML and CSS code from Katelyn
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <title><?php echo $course['CourseName']; ?> Sections</title>
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
      width: 100%; /* Set the width of the sidebar */
      position: fixed; /* Fixed Sidebar (stay in place on scroll) */
      z-index: 1; /* Stay on top */
      left: 250px;
      background-color: #C8102E; 
      overflow-x: hidden; /* Disable horizontal scroll */
      padding-top: 10px;
      top: 50px; /* Adjust the top position to appear below the header */
      z-index: 998;
    }
    .topbar span {
      font-size: 25px;
      color: #fff;
      display: block;
      padding: 4px, 4px, 4px, 4px; 
      padding-left: 20px;
    }
    .search-bar {
      height: 15px;
      margin-left: 850px; /* Adjust as needed */
      margin-top: -33px;
      
     }
     .search-bar input {
      width: 250px;
      height: 30px;
      margin-left: 20px;
     }
     .search-icon {
      color: #fff;
      font-size: 25px; 
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
    
    .upload {
      margin-left: 825px;
      color: #fff;
      width: 150px;
      height: 35px;;
      font-size: 20px;;
      font-family: "Georgia", serif;
      text-align: center;
      background-color: #C8102E;    
    }
    .upload:hover,
    .upload:focus{
      background-color: #002F6C;
    }

    .container {
      max-width: 800px;
      margin: 100px auto;
      text-align: center;
      border: none;
      background-color: #F0F0F0;
      margin-right: 285px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .container span {
      font-size: 35px;
      text-align: center;
    }
    
    .popup-container {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
  }
  /* Style for popup content */
  .popup-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 5px;
    text-align: left;
  }

  .popup-content label,
  .popup-content input {
    display: block;
    margin: 4px auto; /* Adding space between elements */
  }

  h2 {
    text-align: center;
    padding-left: 25px;
    padding-bottom: 10px;;
  }

  /* Style for close button */
  .close-btn {
    color: #aaa;
    float: right;
    margin-top: 5px;
    font-size: 28px;
    font-weight: bold;
  }

  .close-btn:hover,
  .close-btn:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
       
    </style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="topbar">
        <span><?php echo $course['CourseName']; ?>: <?php echo $instructorName; ?></span>
        <div class="search-bar">
            <i class="material-icons search-icon">search</i>
            <input type="text" placeholder="Search...">
        </div>
    </div>
  
    <div class="sidenav">
        <img src="NoteNest.png" alt="Logo" class="logo" width="300">
        <hr>
        <a href="#" class="home"><i class="material-icons">home</i> Home</a>
        <a href="#" class="profile"><i class="material-icons">account_circle</i> Profile</a>
        <a href="#" class="logout"><i class="material-icons">logout</i> Logout</a>
    </div>

    <div class="container">
        <?php if (empty($sections)): ?>
            <p>No sections available for this course.</p>
        <?php else: ?>
            <div class="section-cards">
                <?php foreach ($sections as $section): ?>
                    <div class="section-card">
                        <h3><?php echo $section['SectionName']; ?></h3>
                        <p>Section ID: <?php echo $section['SectionID']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function openPopup() {
            document.getElementById("popup").style.display = "block";
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>
</body>
</html>