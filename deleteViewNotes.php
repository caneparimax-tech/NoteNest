<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Setup database connection
require(__DIR__ . "/connect.php");
//echo 'Role: ' . $_SESSION['CourseID'];
//echo 'Redirecting to: ' . $_SESSION['SectionID'];
//exit;

$error = '';
// Check if the delete request is sent via POST
if (isset($_POST['delete']) && $_POST['delete'] == 'yes') {
    $noteID = $_POST['noteID'];

    // Use try-catch block to catch any PDO exceptions
    try {
        // SQL to delete a record
        $sql = "DELETE FROM Notes WHERE NoteID = ?";
        $stmt = $dbconnect->prepare($sql);
        
        // Attempt to execute the prepared statement
        if ($stmt->execute([$noteID])) {
            // After deletion, redirect based on user role
            $redirectPage = ($_SESSION['Role'] == 'Instructor') ? "instructorCourseSectionHome.php" : (($_SESSION['Role'] == 'Student') ? "studentUserHomepage.php" : "adminCourseSectionHome.php");
            header("Location: $redirectPage");
            exit;
        } else {
            // If execute returns false, log the error info array
            $errorInfo = $stmt->errorInfo();
            $error = "Deletion failed: " . $errorInfo[2];
            error_log("Deletion failed: " . print_r($errorInfo, true));
        }
    } catch (PDOException $e) {
        // Catch any PDO exception and log the error
        $error = "PDO Exception on delete: " . $e->getMessage();
        error_log("PDO Exception on delete: " . $e->getMessage());
    }
}


$title = isset($_GET['title']) ? htmlspecialchars(urldecode($_GET['title'])) : 'No Title';
$noteDesc = isset($_GET['noteDesc']) ? htmlspecialchars(urldecode($_GET['noteDesc'])) : 'No Description';
$date = isset($_GET['date']) ? htmlspecialchars(urldecode($_GET['date'])) : 'No Date';
$author = isset($_GET['author']) ? htmlspecialchars(urldecode($_GET['author'])) : 'No Author';
$noteID = isset($_GET['noteID']) ? $_GET['noteID'] : null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

  <title>View Note</title>
  <style>
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
      margin-top: 15px;
      font-size: 35px;
      text-align: center;
    }
    .right-sidebar {
      height: calc(100% - 50px);
      width: 50px;
      position: fixed;
      z-index: 1;
      top: 50px;
      right: 0;
      background-color: #c4e0ef; 
      overflow-x: hidden;
      transition: 0.5s;
      padding-top: 40px;
    }
    .right-sidebar-content {
      padding: 50px;
    }
    /* Plus and minus button styles */
    .collapse-btn {
      position: absolute;
      top: 70px;
      right: 10px;
      cursor: pointer;
      font-size: 24px;
      color: #002F6C;
      z-index: 998;
      text-align: center;
    }
    .collapsed {
      display: none;
    }
    .collapse-btn a {
      margin-left: 5px;
      font-size: 15px;
    }
    .right-sidebar-content p {
      margin-top: 50px;
      text-align: left;
    }
    .delete-button {
      color: #fff;
      width: 100px;
      height: 25px;;
      font-size: 15px;;
      font-family: "Georgia", serif;
      text-align: center;
      background-color: #C8102E; 
     flex-grow: 0; /* Prevents the button from growing */
    border-radius: 10px;
      margin-top: 10px;
    }
.delete-button:hover,
    .upload:focus{
      background-color: #69001F;
      cursor: pointer;

    }

</style>
</head>
<body>
  <header>
    <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
  </header>
  <div class = "topbar">
    <span>Note Viewer</span>
  </div>
  <div class="sidenav">
    <img src="NoteNest.png" alt="Logo" class="logo" width="300">
    <hr>
    <a href="#" class="home"><i class="material-icons">home</i></i> Home</a>
    <a href="#" class="profile"><i class="material-icons">account_circle</i></i> Profile</a>
    <a href="logout.php" class="logout"><i class="material-icons">logout</i></i> Logout</a>
  </div>
  <div class="right-sidebar" id="rightSidebar">
    <div class="collapse-btn" onclick="toggleRightSidebar()">
      <a>Note Info <i class="material-icons" id="collapseIcon">add</i></a>
    </div>
    <div class="right-sidebar-content">
      <p>Title: <?php echo $title; ?></p>
      <p>Author: <?php echo $author; ?></p>
      <p>Description: <?php echo $noteDesc; ?></p>
      <p>Instructor Verfied: YES</p>
      <p>Date Published: <?php echo $date; ?></p>
      
  <form method="POST" id="deleteForm">
      <input type="hidden" name="noteID" value="<?php echo $noteID; ?>">
      <input type="hidden" name="delete" value="yes">
      <button type="submit" class="delete-button" onclick="return confirmDeletion()">Delete Note</button>
  </form>
    </div>
  </div>
  <script>
    function toggleRightSidebar() {
      var sidebar = document.getElementById("rightSidebar");
      var icon = document.getElementById("collapseIcon");
      if (sidebar.style.width === "250px") {
        sidebar.style.width = "50px";
        icon.innerHTML = "add";
      } else {
        sidebar.style.width = "250px";
        icon.innerHTML = "remove";
      }
    }
    function confirmDeletion() {
    console.log("Attempting to delete note with ID:", document.querySelector('[name="noteID"]').value);

    return confirm('Are you sure you want to delete this note?');
}

  </script>
</body>
</html>