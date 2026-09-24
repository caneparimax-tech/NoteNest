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
    <a href="#" class="logout"><i class="material-icons">logout</i></i> Logout</a>
  </div>
  <div class="right-sidebar" id="rightSidebar">
    <div class="collapse-btn" onclick="toggleRightSidebar()">
      <a>Note Info <i class="material-icons" id="collapseIcon">add</i></a>
    </div>
    <div class="right-sidebar-content">
      <p>Title: My Note</p>
      <p>Author: Katelyn</p>
      <p>Description: This is a test.</p>
      <p>Instructor Verfied: YES</p>
      <p>Date Published: Today</p>
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
  </script>
</body>
</html>