<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require(__DIR__ . "/connect.php");

// Define variables and initialize with empty values
$courseName = "";
$courseName_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate course name
    if (empty(trim($_POST["courseName"]))) {
        $courseName_err = "Please enter the course name.";
    } else {
        $courseName = trim($_POST["courseName"]);
    }

    // Check input errors before inserting into database
    if (empty($courseName_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Courses (CourseName) VALUES (?)";

        if ($stmt = $dbconnect->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(1, $param_courseName, PDO::PARAM_STR);

            // Set parameters
            $param_courseName = $courseName;

            if ($stmt->execute()) {
                // Course created successfully, redirect to course list page
                header("Location: allCourses.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

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

    <title>Create Course</title>
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
        .container {
        max-width: 900px;
        height: 450px;
        margin-top: 150px;
        text-align: center;
        margin-right: 285px;
        border-radius: 20px;
        }
        .container a {
        padding: 12px 12px 17px 20px;
        padding-top: 20px;
        text-align: left;
        text-decoration: none;
        font-size: 23px;
        color: #C8102E;
        display: block;
        margin: 12px;
        }
        .container label {
        text-align: left;
        }
        .container a:hover {
            color:#002F6C
        }

    </style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class = "topbar">
        <span>Create a Course</span>
    </div>
    <div class="sidenav">
        <img src="NoteNest.png" alt="Logo" class="logo" width="300">
        <hr>
        <a href="adminUserHomepage.php" class="home"><i class="material-icons">home</i></i> Home</a>
        <a href="adminProfile.php" class="profile"><i class="material-icons">account_circle</i></i> Profile</a>
        <a href="logout.php" class="logout"><i class="material-icons">logout</i></i> Logout</a>
    </div>
    <div class="container">
        <span>
            <b>Please Enter Course Information</b>
        </span>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label><b>Course Name</b></label>
            <input type="text" name="courseName" placeholder="Enter Course Name" value="<?php echo $courseName; ?>">
            <span><?php echo $courseName_err; ?></span>
            <input type="submit" name="submit" value="Create Course">
        </form>
        
    </div>
</body>
</html>