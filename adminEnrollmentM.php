<?php 
    session_start();

// Check if the user is logged in and if the role is 'Admin'
if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require(__DIR__ . "/connect.php");

// Prepare and execute the query to fetch courses associated with the instructor
$query = "SELECT CourseID, CourseCode, CourseName FROM Courses";
$stmt = $dbconnect->prepare($query);
$stmt->execute();
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
    margin-left: 265px;
    /* Add more styling here */
    display: block;
    flex-direction: column; /* Stack children vertically */
    align-items: center; /* Center-align children horizontally */
    justify-content: center; /* Center content vertically (optional) */
   }

.course-selection, .student-add, .student-list, .section-selection {
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
    justify-content: space-between;
    width: 800px;
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
    background-color: #002E6D; /* Darker shade for hover state */
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
 <ul>
    <li><a href= "adminUserHomepage.php" class="home"><i class="material-icons">home</i> Home</a></li>
    <li><a href="adminProfile.php" class="profile"><i class="material-icons">account_circle</i> Profile</a></li>
    <li><a href="logout.php" class="logout"><i class="material-icons">exit_to_app</i> Logout</a></li>
  </ul>

  </div>
  <div class="enrollment-section">
    <h2>Enroll Students in a Course Section</h2>
    <div class="course-selection">
        <label for="course-section">Course</label>
        <select id="course-section" name="course-section">
    <?php foreach ($courses as $course): ?>
        <option value="<?php echo htmlspecialchars($course['CourseID']); ?>">
            <?php echo htmlspecialchars($course['CourseCode'] . " - " . $course['CourseName']); ?>
        </option>
    <?php endforeach; ?>
</select>
<div class="section-selection">
    <label for="section-section">Instructor Name</label>
    <select id="section-section" name="section-section">
  <option value="NUMERIC_SECTION_ID">Instructor Name - Additional Details</option>
    </select>
</div>
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
    var sectionSelect = document.getElementById("section-section");
    var sectionID = sectionSelect.value;
    var studentIdInput = document.getElementById("student-id");
    var studentId = studentIdInput.value;
    var courseSelect = document.getElementById("course-section");
    // This will now contain both CourseCode and CourseName
    var courseText = courseSelect.options[courseSelect.selectedIndex].text;

    if (sectionID && studentId) { // Ensure both sectionID and studentId are present
        // Create a FormData object
        let formData = new FormData();
        formData.append('sectionID', sectionID); // Send the sectionID to the server
        formData.append('studentId', studentId);

        // Perform the fetch request
        fetch('addStudent.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                // Split the courseText to separate the CourseCode and CourseName
                const [courseCode, courseName] = courseText.split(' - ');

                // Add the student to the list with the course code and name for display purposes
                var studentList = document.querySelector('.student-list ul');
                var newStudent = document.createElement('li');
                selectedStudents.push({
                    studentId: studentId,
                    sectionId: sectionID,
                    courseCode: courseCode,
                    courseName: courseName
                });
                newStudent.innerHTML = `
                    <span class="student-detail">${courseCode} - ${courseName}</span>
                    <span class="student-detail">${studentId}</span> 
                    <span class="student-detail">${response.studentName}</span>
                `;
                studentList.appendChild(newStudent);

                // Clear the input field
                studentIdInput.value = '';
            } else {
                // Handle errors
                alert(`Error: ${response.error}`);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An error occurred while adding the student.');
        });
    } else {
        alert("Please select a section and enter a student ID.");
    }
}

function confirmSelection() {
    // Check if any students are selected
    if (selectedStudents.length === 0) {
        alert("No students selected.");
        return;
    }

    var dataToSend = {
        students: selectedStudents.map(student => ({
            studentId: student.studentId,
            sectionId: student.sectionId // Each student's sectionId is used
        }))
    };

    fetch('enrollStudents.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dataToSend)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Students have been enrolled successfully.");
            // Clear the student list
            var studentList = document.querySelector('.student-list ul');
            studentList.innerHTML = ''; // Clear the list in a more performant way
            selectedStudents = [];
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
        alert("There was a problem with the request.");
    });
}

function updateSectionsDropdown(courseId) {
    // Prepare the data to be sent in the POST request
    let formData = new URLSearchParams();
    formData.append('courseId', courseId);

    // Perform the fetch request
    fetch('fetchSections.php', {
        method: 'POST',
        body: formData,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
    let instructorDropdown = document.getElementById("section-section");
    instructorDropdown.innerHTML = ""; // Clear the dropdown

    if (data.success) {
        data.sections.forEach(section => {
            let option = document.createElement("option");
            option.textContent = section.instructorName; // Set text content to the instructor's name
            option.value = section.sectionID; // Set the option's value to the sectionID
            instructorDropdown.appendChild(option);
        });
    } else {
        alert(data.error || "No sections found for the selected course.");
    }
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error.message);
        alert("There was a problem fetching the instructors.");
    });
}

window.onload = function() {
    // Get the first course ID from the dropdown or set a default
    var initialCourseId = document.getElementById("course-section").value;
    updateSectionsDropdown(initialCourseId);
};

// Add event listener to the course dropdown to update sections on change
document.getElementById("course-section").addEventListener("change", function() {
    updateSectionsDropdown(this.value);
});

</script>
</body>
</html>