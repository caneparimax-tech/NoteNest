<?php 
    session_start();

// Check if the user is logged in and if the role is 'Instructor'
if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Instructor') {
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=mjcanepa', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// Assuming the instructor's ID is correctly stored in the session as 'instructorId'
$instructorId = $_SESSION['instructorId'];

// Prepare and execute the query to fetch courses associated with the instructor
$query = "SELECT c.CourseName, c.CourseID FROM Courses c JOIN Sections s ON c.CourseID = s.CourseID WHERE s.InstructorID = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$instructorId]);

// Fetch all courses associated with the instructor
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
      margin-bottom: -10px;

    }
    /* When you mouse over the navigation links, change their color */
    .sidenav a:hover {
      color: #002F6C;
      background-color: #e3eef4;
    }
    .sidenav img {
      padding-top: 35px;
      padding-right: 70px;
    }
    .sidenav hr {   
      margin: 20px;
      height: 2px; /* thickness of the line */
      background-color: #002F6C; /* colour of the line */
      border: none;
    }
    .sidenav a.enrollment { /*"home" refers to the class or current page we are on since we want to highlight that*/
      background-color: #e3eef4;
      color: #002F6C;
      border-radius: 2px;
      border-left: 4px solid #002F6C;
    }
    .sidenav ul {
      list-style-type: none; /* Removes the bullet points */
      padding-left: 0; /* Removes padding on the left side, which sometimes affects the bullet points */
      margin: 0; /* Resets any default margin */
    }

    .sidenav li {
      padding-bottom: 10px; /* Adjust this value to increase or decrease the spacing */
    }
    .welcome {
      max-width: 800px;
      margin: 100px auto;
      text-align: center;
      border: none;
      background-color: #F0F0F0;
      padding-top: 20px;
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

.enrollment-section {
    width: 80%; /* Adjust based on your layout needs */
    margin: auto; /* Centers the section */
    text-align: center; /* Center-aligns the text */
    padding: 20px;
    margin-top: 115px;
    margin-left: 275px;
    /* Add more styling here */
    display: block;
  flex-direction: column; /* Stack children vertically */
  align-items: center; /* Center-align children horizontally */
 justify-content: center; /* Center content vertically (optional) */
   }

.course-selection, .student-add, .student-list {
    margin-bottom: 10px; /* Adds space between the sections */
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
    margin-bottom: 5px; /* Adds space between list items */
    padding: 10px 4px 10px 4px;
    text-align: center; /* Aligns text to the left */
    width: auto; /* or any other value that suits your design */
    max-width: 500px;
    margin: inherit;
}

/* Style for the dropdown and input field */
select {
    margin-right: 10px; /* Adds some space to the right */
    /* Add more styling here */
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
    margin-top: -100px;
    margin-left: -14px;
        /* Add more button styling here */
    border: 1px solid black;
    transition: background-color 0.3s ease;
    box-sizing: border-box;
    background-color: white;
      flex: none; /* This will prevent the button from growing or shrinking */
        vertical-align: top; /* Align button top with input field */
    vertical-align: bottom; /* Align button top with input field */
    horizontal-align: left;
    height: 38px;
    width: 38px;

}

/* Confirm selection button style */
.confirm-selection-button {
    margin-left: 0px;
    cursor: pointer;
    padding: 10px; /* Adjust padding as needed */
    margin-top: 10px;
    border: 1px solid black;
    width: 200px;
    font-size: 16px;
    background-color: white; /* Example background color */
    text-align: center; /* Ensure text is centered */
        /* Additional styles for interaction */
    transition: background-color 0.3s ease;
}
.confirm-selection-button:hover {
    background-color: darkgrey; /* Darker shade for hover state */
}
button:hover {
    background-color: darkgrey; /* Darker shade for hover state */
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
 <ul>
    <li><a href= "instructorUserHomepage.php" class="home"><i class="material-icons">home</i> Home</a></li>
    <li><a href="instructorProfile.php" class="profile"><i class="material-icons">account_circle</i> Profile</a></li>
    <li><a href="pendingNotes.php" class="pending"><i class="material-icons">pending_actions</i> Pending</a></li>
    <li><a href="enrollment.php" class="enrollment"><i class="material-icons">school</i> Enrollment</a></li>
    <li><a href="logout.php" class="logout"><i class="material-icons">exit_to_app</i> Logout</a></li>
  </ul>

  </div>
  <div class="enrollment-section">
    <h2>Enroll Students to Course Section</h2>
    <div class="course-selection">
        <label for="course-section">Course Section</label>
        <select id="course-section" name="course-section">

        <?php foreach ($courses as $course): ?>
            <option value="<?php echo htmlspecialchars($course['CourseID']); ?>">
                <?php echo htmlspecialchars($course['CourseName']); ?>
            </option>
        <?php endforeach; ?>        </select>
    </div>
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
    
          <header>
        <h1>University of Mississippi</h1>
    </header>
<!-- Loop through the fetched courses and display them -->
    
<script>
var selectedStudents = [];
function addStudent() {
    const courseSelect = document.getElementById("course-section");
    const courseId = courseSelect.value;
    const courseName = courseSelect.options[courseSelect.selectedIndex].text;
    const studentId = document.getElementById("student-id").value;

    if (courseId && studentId) {
        const data = new URLSearchParams();
        data.append("courseId", courseId);
        data.append("studentId", studentId);

        fetch("add_student.php", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                const studentList = document.querySelector('.student-list ul');
                const newStudent = document.createElement('li');
                newStudent.innerHTML = `<span class="student-detail">${courseName}</span><span class="student-detail">${response.studentId}</span><span class="student-detail">${response.studentName}</span>`;
                studentList.appendChild(newStudent);
                // Update the selectedStudents array
                selectedStudents.push({
                    studentId: response.studentId,
                    studentName: response.studentName,
                    courseId: courseId
                });
                document.getElementById("student-id").value = ''; // Clear the input field
            } else {
                console.error(response.error); // Handle error
            }
        })
        .catch(error => console.error('Error:', error));
    } else {
        alert("Please select a course and enter a student ID.");
    }
}
function confirmSelection() {
    if (selectedStudents.length === 0) {
        alert("No students selected.");
        return;
    }

    const dataToSend = JSON.stringify({
        students: selectedStudents,
        instructorId: "<?php echo $_SESSION['instructorId']; ?>" // Ensure this PHP code runs on server-side script rendering
    });

    fetch("enroll_students.php", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: dataToSend
    })
    .then(response => response.json())  // Make sure the server's response is handled as JSON
    .then(response => {
        if (response.success) {
            alert("Students have been enrolled successfully.");
            const studentList = document.querySelector('.student-list ul');
            while (studentList.firstChild) {
                studentList.removeChild(studentList.firstChild);
            }
            selectedStudents = [];
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