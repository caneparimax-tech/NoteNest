<?php 
    session_start();

    //check if user is logged in and is an admin, if not redirect to login page
    if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
        header("Location: login.php");
        exit();
    }
    require(__DIR__ . "/connect.php");

// Define variables and initialize with empty values
$courseCode = "";
$courseCode_err = "";

$courseName = "";
$courseName_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate course code
    if(empty(trim($_POST["courseCode"]))) {
        $courseCode_err = "Please enter the course code.";
    } else {
        $courseCode = trim($_POST["courseCode"]);
    }

    // Validate course name
    if(empty(trim($_POST["courseName"]))) {
        $courseName_err = "Please enter the course name.";
    } else {
        $courseName = trim($_POST["courseName"]);
    }

    // Check input errors before inserting into database
    if(empty($courseCode_err) && empty($courseName_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Courses (CourseCode, CourseName) VALUES (?, ?)";

        if($stmt = $dbconnect->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(1, $param_courseCode, PDO::PARAM_STR);
            $stmt->bindParam(2, $param_courseName, PDO::PARAM_STR);

            // Set parameters
            $param_courseCode = $courseCode;
            $param_courseName = $courseName;

            // Attempt to execute the prepared statement
            if($stmt->execute()) {
                // Course created successfully, redirect to course list page
                header("Location: allCourses.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $dbconnect->close();
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

  <title>Homepage</title>
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
    .main-content {
      width: calc(100% - 250px);
      max-width: 100%;
      margin-left: 250px;
      padding: 20px; 
    }
    .welcome {
      max-width: 800px;
      margin: 100px auto;
      text-align: center;
      border: none;
      background-color: #F0F0F0;
      padding-top: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .welcome span {
      font-size: 45px;
      text-align: center;
      padding-top: 20px;
      margin-top: 10px;
    }
    
    .container {
      width: 600px;
      height: 500px;
      margin-top: -65px;
      text-align: center;
      border-radius: 20px;
    }
    .container a {
      padding: 12px 12px 13px 20px;
      padding-top: 20px;
      text-align: left;
      text-decoration: none;
      font-size: 23px;
      color: #C8102E;
      display: block;
      margin: 12px;
    }
    .container a:hover {
        color:#002F6C
    }
    /* Add this style for the modal */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%; 
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content/Box */
    .modal-content {
        margin: auto;
        background-color: #fefefe;
        margin-top: 15%;
        text-align: center;
        padding: 20px;
        border: 1px solid #888;
        max-width: 50%;
        justify-content: center;
    }

    /* Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

</style>
<script>
  // Function to display the popup modal
  function openCreateCourseModal() {
      document.getElementById("createCourseModal").style.display = "block";
  }

  // Function to close the popup modal
  function closeCreateCourseModal() {
      document.getElementById("createCourseModal").style.display = "none";
  }
</script>
</head>
<body>
  <header>
    <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
  </header>
  <div class = "topbar">
    <span>Homepage</span>
  </div>
  <div class="sidenav">
    <img src="NoteNest.png" alt="Logo" class="logo" width="300">
    <hr>
    <a href="adminUserHomepage.php" class="home"><i class="material-icons">home</i></i> Home</a>
    <a href="adminProfile.php" class="profile"><i class="material-icons">account_circle</i></i> Profile</a>
    <a href="logout.php" class="logout"><i class="material-icons">logout</i></i> Logout</a>
  </div>
  <div class="main-content">
    <div class="welcome">
      <!-- Your main content goes here -->
      <span>Welcome Admin!</span>
    </div>
    <div class="container">
      <span>
          <b>What would you like to do?</b>
      </span>
      <a href="addAdmin.php" class="createAdmin"><i class="material-icons">supervisor_account</i></i> Create a new admin account</a>
      <a href="#" class="home" onclick="openCreateCourseModal()"><i class="material-icons">add_circle</i></i> Create a new course</a>
      <a href="adminEnrollment.php" class="enrollment"><i class="material-icons">group_add</i></i> Enroll students in a course section </a>
      <a href="allCourses.php" class="logout"><i class="material-icons">text_increase</i></i> View all courses</a>
      <a href="allInstructors.php" class="logout"><i class="material-icons">hail</i></i> View all instructors</a>
      <a href="allStudents.php" class="logout"><i class="material-icons">school</i></i> View all students</a>
      <div id="createCourseModal" class="modal">
      <div class="modal-content">
          <span class="close" onclick="closeCreateCourseModal()">&times;</span>
          <form action="adminUserHomepage.php" method="post">
            <label><b>Create a New Course</b></label>
            <input type="text" name="courseCode" placeholder="Enter Course Code" value="<?php echo $courseCode; ?>">
            <span><?php echo $courseCode_err; ?></span>
            <input type="text" name="courseName" placeholder="Enter Course Name" value="<?php echo $courseName; ?>">
            <span><?php echo $courseName_err; ?></span>
            <input type="submit" name="submit" value="Create Course">
          </form>
      </div>
  </div>
</div>
 
</body>
</html>
