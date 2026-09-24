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

  <title>Add Admin</title>
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
    <span>Add a New Admin Account</span>
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
        <b>Add New Admin Account</b>
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

        <input type="submit" name="submit" value="Add Admin Account">
    </form>
  </div>
</body>
</html>
