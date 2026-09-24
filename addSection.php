<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is an admin, if not redirect to login page
if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require(__DIR__ . "/connect.php");

$sql_instructors = "SELECT * FROM Instructors ORDER BY LName ASC";

try {
    $stmt_instructors = $dbconnect->prepare($sql_instructors);
    $stmt_instructors->execute();
    $instructors = $stmt_instructors->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header("Location: error.php?message=Database error: " . $e->getMessage());
    exit();
}

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $instructorId = $_POST['instructor_id'];
    $courseId = $_GET['id'];

    // Check if instructor is already assigned to this course
    $checkSql = "SELECT * FROM Sections WHERE CourseID = :courseId AND InstructorID = :instructorId";
    $checkStmt = $dbconnect->prepare($checkSql);
    $checkStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
    $checkStmt->bindParam(':instructorId', $instructorId, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Instructor is already assigned to this course.'
        ]);
        exit();
    }

    // Proceed with inserting the new section as the instructor is not assigned
    $sql = "INSERT INTO Sections (CourseID, InstructorID) VALUES (:courseId, :instructorId)";

    
    try {
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->bindParam(':instructorId', $instructorId, PDO::PARAM_INT);
        $stmt->execute();

        // Get the last inserted ID
        $lastInsertedId = $dbconnect->lastInsertId();
        
       
        // Fetch the instructor's name
        $instructorName = ""; // Initialize with an empty string
        $stmt_instructor = $dbconnect->prepare("SELECT FName, LName FROM Instructors WHERE InstructorID = :instructorId");
        $stmt_instructor->bindParam(':instructorId', $instructorId, PDO::PARAM_INT);
        $stmt_instructor->execute();
        $instructor = $stmt_instructor->fetch(PDO::FETCH_ASSOC);
        if ($instructor) {
            $instructorName = $instructor['FName'] . ' ' . $instructor['LName'];
        }

        echo json_encode([
            'success' => true,
            'sectionId' => $lastInsertedId,
            'instructorName' => $instructorName
        ]);
    } catch (PDOException $e) {
        // Return an error message in JSON format
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    // If the request is not POST, return an error
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}

exit();?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <title>Add Section</title>
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
            padding: 4px 4px 4px 4px; 
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
        .container a:not(.admin-button) {
            padding: 12px 12px 17px 20px;
            padding-top: 20px;
            text-align: left;
            text-decoration: none;
            font-size: 23px;
            color: #C8102E;
            display: block;
            margin: 12px;
        }
        .container a:not(.admin-button):hover {
            color:#002F6C
        }
        .admin-button {
            margin-left: 200px;
            background-color: #C8102E;
            color: #fff;
            cursor: pointer;
        }
        .admin-button:hover {
            background-color: #002F6C;
            color: #fff;
        }
        .section-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            margin-top: 100px;
            margin-left: 350px;
        }
        .section-card {
            width: 300px; /* Adjust card width as needed */
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow effect */
            margin: 20px;
            padding: 20px;
        }
        .section-name {
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            color: #333;
        }

    </style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class = "topbar">
        <span>Add Course Section</span>
    </div>
    <div class="sidenav">
        <img src="NoteNest.png" alt="Logo" class="logo" width="300">
        <hr>
        <a href="adminUserHomepage.php" class="home"><i class="material-icons">home</i></i> Home</a>
        <a href="adminProfile.php" class="profile"><i class="material-icons">account_circle</i></i> Profile</a>
        <a href="logout.php" class="logout"><i class="material-icons">logout</i></i> Logout</a>
    </div>
    <div class="container">

        <h2>Add Course Section</h2>
        <form method="post">
            <label for="instructor-id">Instructor:</label>
            <select name="instructor_id" id="instructor-id">
                <option></option>
                <?php foreach ($instructors as $instructor): ?>
                    <option value="<?php echo $instructor['InstructorID']; ?>">
                        <?php echo $instructor['FName'] . ' ' . $instructor['LName']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
            <input type="submit" name="submit" value="Add Section">
        </form>
    </div>
</body>
</html>