<?php 
session_start();

// Check if the user is logged in and if the role is 'Instructor'
if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Instructor') {
    header("Location: login.php");
    exit();
}

require(__DIR__ . "/connect.php");
$instructorId = $_SESSION['instructorId'];

// Prepare and execute the query to fetch courses associated with the instructor
$query = "SELECT c.CourseID, c.CourseCode, c.CourseName, s.SectionID FROM Courses c JOIN Sections s ON c.CourseID = s.CourseID WHERE s.InstructorID = ?";
$stmt = $dbconnect->prepare($query);
$stmt->execute([$instructorId]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

  <title>Enrollment</title>
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
    .sidenav a.enrollment { /*"home" refers to the class or current page we are on since we want to highlight that*/
      background-color: #e3eef4;
      color: #002F6C;
      border-radius: 2px;
      border-left: 4px solid #002F6C;
    }
    .enrollment-section {
        width: calc(100% - 250px);
        margin-left: 250px;
        text-align: center; /* Center-aligns the text */
        padding: 20px;
        margin-top: 115px;
        display: block;
        flex-direction: column; /* Stack children vertically */
        align-items: center; /* Center-align children horizontally */
        justify-content: center; /* Center content vertically (optional) */
    }
    .course-selection, .student-add, .student-list {
        margin-bottom: 15px; /* Adds space between the sections */
        margin-top: 15px;
    }
    .student-list ul {
        list-style-type: none; /* Removes the default list bullet */
        padding: 0; /* Removes the default padding */
        width: 100%; /* Full width of its parent */
        display: flex;
        flex-direction: column;
        align-items: center; /* Center-align items */
    }
    .student-list li {
        /* Style for the student list items */
        background-color: #f0f0f0; /* Example background color */
        border: 2px dotted black; /* Example border */
        margin-bottom: 20px; /* Adds space between list items */
        padding: 10px 4px 10px 4px;
        text-align: center; /* Aligns text to the left */
        width: auto; /* or any other value that suits your design */
        width: 800px;
        margin: inherit;
    }
    /* Style for the drodbconnectwn and input field */
    select {
        margin-right: 10px; /* Adds some space to the right */
        width: 50%; /* or any other value that suits your design */
        max-width: 300px;
        border-color: black;
        border-radius: 0; 
        background-color: white; /* Example background color */
        box-sizing: border-box;
    }
    select, input[type="text"] {
        margin-right: 10px; /* Adds some space to the right */
        /* Add more styling here */
        width: 50%; /* or any other value that suits your design */
        max-width: 300px;
        border-color: black;
        border-radius: 0; 
        background-color: white; /* Example background color */
        box-sizing: border-box;
    }
    button {
      cursor: pointer;
        margin-bottom: 15px;
        margin-left: -10px;
        font-size: 30px;
        color: #C8102E;
        border: none;
        transition: background-color 0.3s ease;
        background-color: transparent;
        flex: none; /* This will prevent the button from growing or shrinking */
        vertical-align: bottom; /* Align button top with input field */
    }
    /* Confirm selection button style */
    .confirm-selection-button {
      font-family: Georgia, serif;
        color:#fff;
        cursor: pointer;
        padding: 10px; /* Adjust padding as needed */
        margin-top: 10px;
        border: 1px solid black;
        width: 200px;
        font-size: 16px;
        background-color: #C8102E; 
        text-align: center; /* Ensure text is centered */
        transition: background-color 0.3s ease;
        font-weight: bold;
    }
    .confirm-selection-button:hover {
        color:#fff;
        background-color: #002E6D; 
    }
    button:hover {
      color: #002E6D; /* Darker shade for hover state */
    }
    .header-row {
      display: grid;
      grid-template-columns: auto auto auto; /* Creates three columns, adjust as needed */
      justify-content: center;
      gap: 140px; /* Adjust gap between columns */}

    .student-detail:not(:last-child) {
      margin-right: 70px; /* Adjust the space as needed */
    }
</style>
</head>
<body>
  <header>
    <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
  </header>
  <div class = "topbar">
    <span>Enrollment</span>
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
  <div class="enrollment-section">
    <h2>Enroll Students in a Course Section</h2>
    <div class="course-selection">
      <label for="course-section">Course</label>
      <select id="course-section" name="course-section">
  <?php foreach ($courses as $course): ?>
      <option value="<?php echo htmlspecialchars($course['CourseID']); ?>" data-section-id="<?php echo htmlspecialchars($course['SectionID']); ?>">
          <?php echo htmlspecialchars($course['CourseCode'] . " - " . $course['CourseName']); ?>
      </option>
  <?php endforeach; ?>
</select>
    <div class="student-add">
        <label for="student-id">Add Student</label>
        <input type="text" id="student-id" name="student-id" placeholder="Enter ID #" />
        <button type="button" onclick="addStudent()">+</button>
    </div>
    <div class="student-list">
        <h3>Students Selected</h3>
        <div class="header-row">
  <div>Course</div>
  <div>Id Number</div>
  <div>Name</div>
</div>
        <!-- Dynamically populated list of students -->
        <ul>
                        <!-- This is where the students will be placed -->
        </ul>
    </div>
    <button type="button" class="confirm-selection-button" onclick="confirmSelection()">Confirm Selections</button>
</div>
  
    
<script>
const instructorId = "<?php echo $_SESSION['instructorId']; ?>"; // This is set once and used throughout the page lifecycle

var selectedStudents = [];

function addStudent() {
    const courseSelect = document.getElementById("course-section");
    const courseId = courseSelect.value;
    // Retrieve the combined course code and name
    const courseText = courseSelect.options[courseSelect.selectedIndex].text;
    const sectionId = courseSelect.options[courseSelect.selectedIndex].getAttribute('data-section-id');
    const studentIdInput = document.getElementById("student-id");
    const studentId = studentIdInput.value;

    console.log("courseId:", courseId); // Debugging line
    console.log("studentId:", studentId); // Debugging line
    console.log("sectionId:", sectionId); // Debugging line

    if (studentId && sectionId) { // Ensure both sectionID and studentId are present
        let formData = new FormData();
        formData.append('sectionID', sectionId);
        formData.append('studentId', studentId);

        fetch("addStudent.php", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                // Add the student to the selectedStudents array
                selectedStudents.push({
                    studentId: studentId,
                    sectionId: sectionId,
                    courseText: courseText // Now include the combined course code and name
                });

                // Update the HTML of the student list to display both code and name
                const studentList = document.querySelector('.student-list ul');
                const newStudent = document.createElement('li');
                newStudent.innerHTML = `<span class="student-detail">${courseText}</span>
                                        <span class="student-detail">${studentId}</span>
                                        <span class="student-detail">${response.studentName}</span>`;
                studentList.appendChild(newStudent);

                // Clear the student ID input field
                studentIdInput.value = '';
            } else {
                alert(response.error);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    } else {
        alert("Please select a course and enter a student ID.");
    }
}


function confirmSelection() {
    if (selectedStudents.length === 0) {
        alert("No students selected.");
        return;
    }

    // Prepare the data ensuring that each student object includes the required sectionId
    const dataToSend = JSON.stringify({
        students: selectedStudents.map(student => ({
            studentId: student.studentId,
            sectionId: student.sectionId  // Ensure this is included and correctly formatted
        }))
    });

    fetch("enrollStudents.php", {  // Using the same file as for admin enrollment
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: dataToSend
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            alert("Students have been enrolled successfully.");
            const studentList = document.querySelector('.student-list ul');
            while (studentList.firstChild) {
                studentList.removeChild(studentList.firstChild);
            }
            selectedStudents = [];  // Clear the local state of selected students
        } else {
            alert("Error: " + response.error);  // Display errors sent from the server
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert("There was a problem with the request.");
    });
}
</script>
</body>
</html>