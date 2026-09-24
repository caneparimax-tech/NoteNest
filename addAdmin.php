<?php 
    session_start();

    //check if user is logged in and is an admin, if not redirect to login page
    if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
        header("Location: login.php");
        exit();
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

  <title>Create Admin</title>
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
    .main-content {
      width: calc(100% - 250px);
      margin-left: 250px;
      max-width: 2000px;
    }
    .container {
      height: 630px;
      margin-top: 125px;
      text-align: center;
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
    }
    .container a:hover {
        color:#002F6C
    }
    .container span {
      display: block; /* Make the span a block-level element */
      padding-top: 10px;
      margin-bottom: 20px; /* Add space below the heading */
      font-size: 15px; /* Adjust the font size as needed */
      text-align: center; /* Center the text */
    }
    .container form {
      max-width: 500px; /* Adjust the width as needed */
      margin: 0 auto; /* Center the form horizontally */
      text-align: left; /* Align text to the left */
    }

    .container form label {
      display: block; /* Make labels block-level elements */
      margin-bottom: 10px; /* Add spacing between labels */
    }

    .container form input[type="text"],
    .container form input[type="email"],
    .container form input[type="password"] {
      width: 100%; /* Make inputs fill the container width */
      padding: 10px; /* Adjust padding as needed */
      margin-bottom: 20px; /* Add spacing between inputs */
      box-sizing: border-box; /* Include padding and border in the element's total width and height */
    }

</style>
</head>
<body>
  <header>
    <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
  </header>
  <div class = "topbar">
    <span>Create a New Admin Account</span>
  </div>
  <div class="sidenav">
    <img src="NoteNest.png" alt="Logo" class="logo" width="300">
    <hr>
    <a href="adminUserHomepage.php" class="home"><i class="material-icons">home</i></i> Home</a>
    <a href="adminProfile.php" class="profile"><i class="material-icons">account_circle</i></i> Profile</a>
    <a href="logout.php" class="logout"><i class="material-icons">logout</i></i> Logout</a>
  </div>
  <div class=" main-content">
    <div class="container">
      <span>
          <b>To create a new admin, please enter the following information:</b>
      </span>
      <form action="process_add_admin.php" method="post">
          <label for="idNumber"><b>ID Number</b></label>
          <input type="text" name="idNumber" placeholder="Enter ID Number" id="idNumber" required>
                      
          <label for="firstName"><b>First Name</b></label>
          <input type="text" name="firstName" placeholder="Enter First Name" id="firstName" required>

          <label for="lastName"><b>Last Name</b></label>
          <input type="text" name="lastName" placeholder="Enter Last Name" id="lastName" required>

          <label for="email"><b>Ole Miss Email</b></label>
          <input type="email" name="email" placeholder="Enter Email" id="email" required>

          <label for="password"><b>Password</b></label>
          <input type="password" name="password" placeholder="Enter Password" id="password" required>

          <label for="password2"><b>Confirm Password</b></label>
          <input type="password" name="password2" placeholder="Re-Enter Password" id="password2" required>

          <input type="submit" name="submit" value="Create Admin Account">
      </form>
    </div>
  </div>
</body>
</html>
