<?php 
    session_start();
// Check if user is logged in and if the role is 'Instructor'

//if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Instructor') {
//    header("Location: login.php");
//    exit();
//}

// Initialize the PDO object for database connection
    require(__DIR__ . "/connect.php");



// Check if the instructor ID is set in the session

$email = $_SESSION['Email']; // Assuming you have the email in the session

// Prepare the SQL statement to get the InstructorID based on the email
try {
    $stmt = $dbconnect->prepare("
        SELECT i.InstructorID
        FROM Instructors i
        JOIN UserCredentials uc ON i.UserID = uc.UserID
        WHERE uc.Email = :email AND uc.Role = 'Instructor'
    ");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if a row was returned
    if ($result) {
        $_SESSION['instructorId'] = $result['InstructorID'];
        $instructorId = $result['InstructorID'];  // Ensure this variable is used in the subsequent SQL query

    } else {
        echo "No instructor found with that email or the instructor does not have the 'Instructor' role.";
        exit(); // Stop script execution if no instructor is found
    }
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Prepare and execute the SQL query to fetch courses
try {
    $stmt = $dbconnect->prepare("SELECT c.CourseID, c.CourseName, s.SectionID FROM Courses c JOIN Sections s ON c.CourseID = s.CourseID WHERE s.InstructorID = ?");
    $stmt->execute([$instructorId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate HTML for the courses
    foreach ($courses as $course) {
        echo "<div class='course-tab'>" . htmlspecialchars($course['CourseName']) . "</div>";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
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

    /*this will need to be update on every page so the correct button is highlighted*/
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
      padding-top: 50px;
      margin-right: 285px;
      margin-top: 60px;
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
      height: 110px;
      margin-top: -65px;
      text-align: center;
      margin-right: 285px;
      border-radius: 20px;
    }
  
.course-bubble {
    background-color: #F0F0F0; /* Light grey background */
    border: 3px solid #ccc; /* Grey border */
    border-radius: 25px; /* Rounded corners */
    padding: 20px; /* Some padding */
    margin: 10px 0; /* Margin on the top and bottom */
    width: fit-content; /* Adjust width to fit content */
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
    text-align: center; /* Center the text */
    font-size: 20px;
margin-right: -150px;

}

.course-container {
    display: flex;
    flex-direction: column;
    align-items: center; /* Center bubbles in the container */
 text-align: center;
      border: none;
      background-color: #F0F0F0;
      padding-top: 20px;
      font-size: 23px;
margin-bottom: -14px;

}

.course-bubble > div {
    margin: 5px 0; /* Spacing between elements inside the bubble */
      font-size: 20px;

}
</style>
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
    <a href="instructorUserHomepage.php" class="home"><i class="material-icons">home</i></i> Home</a>
    <a href="instructorProfile.php" class="profile"><i class="material-icons">account_circle</i></i> Profile</a>
    <a href="pendingNotes.php" class="pendingNotes"><i class="material-icons">pending_actions</i></i> Pending Notes</a> 
    <a href="instructorEnrollment.php" class="enrollment"><i class="material-icons">group_add</i></i> Enrollment</a> 
    <a href="logout.php" class="logout"><i class="material-icons">logout</i></i> Logout</a>
  </div>
  <div class="welcome">
  <?php if (empty($courses)): ?>
        <span>No Courses Available</span>
    <?php else: ?>
        <span>Courses</span>
            <div class="course-container">
                <?php foreach ($courses as $course): ?>

                    <a href="instructorCourseSectionHome.php?courseID=<?= $course['CourseID'] ?>&sectionID=<?= $course['SectionID'] ?>" class="course-bubble">
                    
                        <div><?= htmlspecialchars($course['CourseName']) ?></div>
                        
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
</div> 
</body>
</html>