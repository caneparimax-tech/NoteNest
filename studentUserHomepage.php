<?php

session_start();
require_once __DIR__ . "/connect.php";

// Redirect to login if not a student or not logged in
if (!isset($_SESSION['Email'], $_SESSION['Role']) || $_SESSION['Role'] !== 'Student') {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['UserID']; // This must match the case exactly as session variable names are case-sensitive

$stmt = $dbconnect->prepare("
    SELECT c.CourseID, c.CourseCode, c.CourseName, s.SectionID
    FROM Courses c
    JOIN Sections s ON c.CourseID = s.CourseID
    JOIN Enrollment e ON s.SectionID = e.SectionID
    JOIN Students st ON e.StudentID = st.StudentID
    WHERE st.UserID = :user_id
");
$stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
$stmt->execute();

$courses = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $courses[] = $row;
}

$stmt = $dbconnect->prepare("
    SELECT n.NoteID, n.NoteTitle, n.NoteDesc, n.UploadDate, n.Verified, n.StudentID
    FROM Notes n
    INNER JOIN Students s ON n.StudentID = s.StudentID
    WHERE s.UserID = :user_id
    ORDER BY n.UploadDate DESC
");
$stmt->execute([':user_id' => $userID]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET['action']) && $_GET['action'] === 'get_notes') {
    // Set the header to tell the browser that we are sending back JSON
    header('Content-Type: application/json');
    
    // Echo the $notes array as a JSON string
    echo json_encode($notes);

    // Stop the script here so no further HTML or data is sent
    exit;
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

    <title>Page</title>
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
            width: calc(100% - 250px); 
            position: fixed; 
            z-index: 1; /* Stay on top */
            left: 250px;
            right: 0;
            position: fixed;
            background-color: #C8102E; 
            overflow-x: hidden; 
            padding: 0px 20px 0px;
            top: 50px; 
            z-index: 998;
            display: flex; 
            align-items: center;
           justify-content: flex-start;
        }
        .topbar span {
            font-size: 25px;
            color: #fff;
            display: block;
            padding: 0px 4px 0px 4px; 
            
           white-space: nowrap;
           overflow: visible;

        }
.breadcrumb-link, .breadcrumb-current {
    color: #fff; 
    text-decoration: none; 
    font-size: 25px;
   
}
.breadcrumb-link:hover {
    color: #002E6D; 
    text-decoration: underline;
}
.breadcrumb-current {
    font-weight: bold; 
    padding-left: 0 !important;
}
.breadcrumb-link .arrow {
    color: #fff;
    font-size: 30px; 
    
}

.search-bar {
  display: flex; /* Establishes a flex container */
  align-items: center; /* Vertically centers the flex items */
    margin-right: max(80px, 15%);
      height: 60px;
    padding-bottom: 25px;

}
     .search-bar input[type="text"] {
width: 200px;
      margin-bottom: 0 !important;
font-size: 14px;
  border-radius:0px 0px 0px 10px;

    border-top: 0px; /* 2px wide, solid, red border */
  border-right: 2px solid black; 
  border-bottom: 2px solid black; 
  border-left: 2px solid black;
    padding-top: 11px;
      padding-bottom: 11px;

      }
input[type="text"]:focus {
    outline: none;
}
     .search-icon {
      color: black;
      font-size: 30px;
      cursor: pointer;
      flex-shrink: 0;
      border-radius: 10px;
      padding: 4px;
      background-color: white;

  border-radius:0px 0px 10px 0px;
  border-right: 2px solid black; 
  border-bottom: 2px solid black;
    }
.search-icon:hover {
      color: red;
      font-size: 30px;
      cursor: pointer;
      flex-shrink: 0;
      background-color: white;
 
 
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
        .my-notes-container {
            width: 100%;
            top: 200px;
            right: 0;
  	    box-sizing: border-box; /* Includes padding and border in the element's total width and height */
            position: relative; /* Position relative for absolute child positioning if needed */
  	    text-align: center;
            align-items: center;
            padding-bottom: 20px; /* Add padding if needed */  
overflow-x: hidden;
            
}
        .my-notes-container a:not(.admin-button) {
            padding: 12px 12px 17px 20px;
            padding-top: 20px;
            text-align: left;
            text-decoration: none;
            font-size: 23px;
            color: #C8102E;
            display: block;
            margin: 12px;

        }
        .my-notes-container a:not(.admin-button):hover {
            color:#002F6C
        }
.header-with-button {
    display: flex;
    justify-content: space-between;
    align-items: center;

}
.header-with-button h2 {
    flex-grow: 1; /* Allows the title to grow */
    text-align: center;
    margin-right: 0px;
 }

        .admin-button:hover {
            background-color: #002F6C !important;
            color: #fff;
            text-decoration: none;
        }
        .section-cards {
          display: flex;
    flex-wrap: wrap;
    justify-content: center; /* This will center the cards within the .section-cards container */
    align-items: flex-start; /* Align items to the start of the cross axis */
margin: 0px auto;   

        }
        .section-card {
             /* Adjust card width as needed */

            background-color: #fff;
            border: 1px solid black;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow effect */
            margin: 20px;
            padding: 10px 20px;
            max-width: 400px;
            display: flex;
            flex-direction: column;
    	    justify-content: center; /* This centers the content vertically */
    	    align-items: center;           
            position: relative;
      cursor: pointer;
            color: black;
    overflow-wrap: break-word; /* Ensures long words will wrap and not overflow */
    word-wrap: break-word; /* For older browsers */
    overflow: hidden;
}
.section-card h3 {
    margin-top: 5px; 
    text-align: center;
}
        .section-name {
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            color: #333;
        }
        .main-content {

    display: flex;
    flex-direction: row;
    align-items: flex-start;
    padding-left: 250px; /* Offset the padding by the width of the sidebar */
    padding-top: 100px;
         }
.no-sections-message {
    text-align: center;
    margin-top: 20px; /* Space below the course title */
    font-size: 1rem; /* Adjust size as needed */
    color: #333; /* Adjust text color as needed */
}
.centered-button {
    display: block;
    background-color: #C8102E !important; /* Red background */
    color: #fff; /* White text */
    padding: 20px;
    margin: 20px;
    border: none; /* No border for the button */
    border-radius: 10px;
    box-shadow: none; /* No shadow for the button */
    text-align: center;
    font-weight: bold;
    width: 270px;
}
.topbar-link {
    color: inherit; /* Make the link use the same color as the span */
    text-decoration: none; /* No underline */
    background-color: transparent; /* Ensure the background is transparent */
}
.dropdown {
  position: relative;
  display: inline-block !important;
  
}

/* Dropdown Content */
.dropdown-content {
  position: absolute;
  background-color: white;
  min-width: 270px !important;
  z-index: 1;
  top: 100% !important; /* Directly under the section card */
  left: 0; /* Aligned to the left edge of the section card */
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px); /* Start slightly above for the dropdown effect */
  border-radius:0px 0px 10px 10px;
    

}
.dropdown-content:hover {f
transition: background-color 0.3s ease;
color: white !important;
background-color: red

}

/* Show the dropdown content on hover */
.section-card:hover {
  opacity: 1; /* Make it visible on hover */
  visibility: visible; /* Show it */
  background-color: grey;
  transition: background-color 0.3s ease;
      background-color: #F0F0F0;

}

/* Style the remove button inside the dropdown */
.remove-btn {
  color: white;
  padding-top: 5px !important;
  padding-bottom: 5px !important;
  padding-right: 50px !important;
  padding-left: 50px !important;
  min-width: 298px !important;
  text-decoration: none;
  display: block;
  font-size: 0.8rem !important;
  text-align: center!important;
  margin: 0px !important;
  

}

.remove-btn:hover {
background-color: red
text-color: white;
transition: background-color 0.3s ease;
color: white !important;

}

.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}



  /* Style for close button */
  .close-btn {
    color: #aaa;
    float: right;
    margin-top: 5px;
    font-size: 28px;
    font-weight: bold;
  }

.upload {
      color: #fff;
      width: 150px;
      height: 35px;;
      font-size: 20px;;
      font-family: "Georgia", serif;
      text-align: center;
      background-color: #C8102E; 
     flex-grow: 0; /* Prevents the button from growing */
    border-radius: 10px;
    }
    .upload:hover,
    .upload:focus{
      background-color: #002F6C;
    }
.sort-button {
      color: #fff;
      width: 150px;
      height: 30px;;
      font-size: 15px;;
      font-family: "Georgia", serif;
      text-align: center;
      background-color: #C8102E; 
     flex-grow: 0; /* Prevents the button from growing */
  border-radius: 0 0 10px 10px;
    border-top: 0px; /* 2px wide, solid, red border */
  border-right: 2px solid black; 
  border-bottom: 2px solid black; 
  border-left: 2px solid black;
 

}
.sort-button:hover,
    .upload:focus{
      background-color: #002F6C;
      cursor: pointer;

    }

.upload {
      color: #fff;
      width: 150px;
      height: 35px;;
      font-size: 20px;;
      font-family: "Georgia", serif;
      text-align: center;
      background-color: #C8102E; 
     flex-grow: 0; /* Prevents the button from growing */
    border-radius: 10px;
     margin: 0px 15px;
    }
    .upload:hover,
    .upload:focus{
      background-color: #002F6C;
      cursor: pointer;

    }
.section-card-link {
    text-decoration: none; /* Removes underline from links */
    color: inherit; /* Inherits text color from parent elements */
    display: block; /* Makes the link block-level to fill the container */

}
#sortStatus {
    white-space: nowrap; 
    font-size: 20px; /* Adjust font size as needed */
    color: black; /* Match the theme color or choose what fits */
    width: 100%;
    font-weight: bold;
}
#sortStatusContainer {
    height: 20px; /* Reserve space for one line of text */
    overflow: hidden; /* Prevent content from spilling outside the container */
    width: 100%;
    text-align: center;
    display: none; /* Hide the status container by default */
}
.verified {
    color: green;
}

.unverified {
    color: red;
}

.course-tab {
    height: 200px;
    max-height: 500px; /* Adjust based on the desired viewport */

    width: calc(100% - 250px); 
    position: absolute;
  
    background-color: #F0F0F0; 
    padding: 10px; 
    padding-right: 20px; /* To prevent content from being hidden by scrollbar */
    margin-right: 20px; 
    display: flex; /* To use flexbox for inner items, if needed */
    justify-content: space-between; /* Adjust as needed, if you have multiple items inside */
    box-sizing: border-box;    
     z-index: 1; /* Keep it above other elements */
border: 2px solid black;
  border-right: 0px;
    overflow-y: auto; /* Enables vertical scrolling */


}
.course-container {
    display: flex;
    flex-wrap: wrap; /* Allows items to wrap onto the next line */

    gap: 10px;
    padding: 20px;
    padding-bottom: 0px;
    justify-content: center; /* Align items to the start of the main axis */
    top: 50px; /* Adjust to place it right below the red bar */
    right: 0;
    box-sizing: border-box;
    min-height: 100px;
position: absolute;
    width: 100%;
    margin: 0 auto;
}
.course-card {
    min-height: 110px;
    min-width: 115px;
    border: 1px solid #333;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    background-color: #fff;
    text-align: center;
    max-width: 280px;
    flex: 0 0 10%; 
    margin-bottom: 10px;
    text-decoration: none;
    transition: background-color 0.3s;
}
.course-card:hover {
    background-color: #f8f8f8; 
    text-decoration: none; 
}
.course-title {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}
.course-description {
    font-size: 14px;
    color: #666;
}

.header-with-sort-search {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-height: 60px;
}
.header-with-sort-search h2 {
    text-align: center;
    margin-left: auto;
    margin-right: auto;
    margin-top: 10px;
    min-width: 150px;
    width: 150px;

 }
.sort-container {
    display: flex;
    flex-direction: column;
    align-items: center;
margin-left: max(80px, 15%);
   min-width: 240px;

width: 240px;
    padding-bottom: 10px;
    height: 62px;
    
 } 
background-color: #f0f0f0; /* Light grey, for example */
    border-width: 0px;
    border-radius: 4px;
    padding: 5px;
    margin-left: 20px;
}
.show-sort-status {
    display: block;
}
.course-header {
              height: 50px; /* Full-height: remove this if you want "auto" height */
            min-width: 830px;
            width: 100%;

            background-color: #F0F0F0; 
            display: flex; 
            align-items: center;
           justify-content: center;
position: absolute;
    left: 0;
        }
.sort-active {
      background-color: #F0F0F0; 
    border: 2px solid black; 
   border-top: 0px;
  border-radius: 0 0 10px 10px;
   min-width: 240px;
}
.no-notes-message {
  
    min-width: 830px;
    width: 100%;
    background-color: #F0F0F0;
    display: flex;
    align-items: center;
    justify-content: center;
     left: 0px;
    position: absolute;
}

     </style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="topbar">
        <a href="studentUserHomepage.php" class="breadcrumb-link">Home</a>
    </div>

    <div class="main-content">

        <div class="sidenav">
            <img src="NoteNest.png" alt="Logo" class="logo" width="300">
            <hr>
            <ul>
                <li><a href="studentUserHomepage.php" class="home"><i class="material-icons">home</i> Home</a></li>
                <li><a href="studentProfile.php" class="profile"><i class="material-icons">account_circle</i> Profile</a></li>
                <li><a href="logout.php" class="logout"><i class="material-icons">exit_to_app</i> Logout</a></li>
            </ul>
        </div>

        <div class="course-tab">
            <h2 class="course-header">My Courses</h2>

   <div class="course-container">
    <?php foreach ($courses as $course): ?>
        <a href="teststudentSectionHomepage.php?courseID=<?php echo urlencode($course['CourseID']); ?>&sectionID=<?php echo urlencode($course['SectionID']); ?>" class="course-card">
            <div class="course-title"><?php echo htmlspecialchars($course['CourseCode']); ?></div>
            <div class="course-description"><?php echo htmlspecialchars($course['CourseName']); ?></div>
        </a>
    <?php endforeach; ?>
</div>
        </div>
        <div class="my-notes-container">
            <div class="header-with-sort-search">
<div class="sort-container">
    <button class="sort-button" onclick="toggleSort()">Sort by Date</button>
    <div id="sortStatusContainer">
        <span id="sortStatus">Test Test</span>
    </div>
</div>
              <h2>My Notes</h2>
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                    <i class="material-icons search-icon" id="search-icon">search</i>
                </div>
               
            </div>
            
            <div class="section-cards" id="section-cards">
                <!-- Notes will be populated here by JavaScript -->
            </div>
        </div>
    </div>

<script>
var currentStudentId = <?php echo json_encode($_SESSION['StudentID']); ?>;

window.addEventListener('load', equalCardSizes);

function updateNotesDisplay(notes) {
    const cardsContainer = document.getElementById('section-cards');
    cardsContainer.innerHTML = ''; // Clear existing notes

    if (notes.length === 0) {
        // Display a message if no notes are found
        cardsContainer.innerHTML = '<div class="no-notes-message">No notes found.</div>';
    } else {
        notes.forEach(note => {
            // Format the date
            const formattedDate = new Date(note.UploadDate).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            cardsContainer.innerHTML += `
<a href="${note.StudentID == currentStudentId ? 'deleteViewNotes.php' : 'viewNotes.php'}?noteID=${note.NoteID}&title=${encodeURIComponent(note.NoteTitle)}&noteDesc=${encodeURIComponent(note.NoteDesc)}&date=${encodeURIComponent(note.UploadDate)}&author=${encodeURIComponent(note.StudentFName + ' ' + note.StudentLName)}&filePath=${encodeURIComponent(note.FilePath)}&verified=${encodeURIComponent(note.Verified ? 'Yes' : 'No')}" class="section-card-link">
        <div class="section-card">
            <h3>Title: ${note.NoteTitle}</h3>
            <p>Publisher: ${note.FName} ${note.LName}</p>
            <p class="verification-status ${note.isVerified ? 'Verified' : 'unverified'}">${note.isVerified ? 'Verified and reliable.' : 'Pending verification'}</p>
            <p class="upload-date">Upload Date: ${new Date(note.UploadDate).toLocaleDateString()}</p>
        </div>
    </a>
`;
        });
    }
}

function fetchAndDisplayNotes() {
    console.log('Fetching notes for the logged-in student');

    return fetch('studentUserHomepage.php?action=get_notes', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(notes => {
        console.log('Notes received:', notes);
        updateNotesDisplay(notes);
        equalCardSizes();
    })
    .catch(error => {
        console.error('Error fetching notes:', error);
    });
}
// Function to fetch and display notes
// Function to fetch and display notes for the logged-in student
function fetchAndDisplayNotes() {
    console.log('Fetching notes for the logged-in student');

    return fetch('studentUserHomepage.php?action=get_notes', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(notes => {
        console.log('Notes received:', notes);
        updateNotesDisplay(notes);
        equalCardSizes();
    })
    .catch(error => {
        console.error('Error fetching notes:', error);
    });
}



function equalCardSizes() {
  var cards = document.querySelectorAll('.section-card');
  var maxHeight = 0;

  // Reset the height so we can measure the natural content height
  cards.forEach(function(card) {
    card.style.height = '';
  });

  // Find the tallest card
  cards.forEach(function(card) {
    if (card.offsetHeight > maxHeight) {
      maxHeight = card.offsetHeight;
    }
  });

  // Set all cards to the same height
  cards.forEach(function(card) {
    card.style.height = maxHeight + 'px';
  });
}

// // Call this function after fetching notes, uploading a new note, resizing the window, and after a search
document.addEventListener('DOMContentLoaded', equalCardSizes);
window.addEventListener('resize', equalCardSizes);


let sortState = 'unsorted'; // Initialize sortState at the top level, outside any function

function toggleSort() {

    const cardsContainer = document.querySelector('.section-cards');
    let cards = Array.from(cardsContainer.children);
    let sortStatus = document.getElementById('sortStatus'); // Get the sort status element
    let sortContainer = document.querySelector('.sort-container'); // Container for sort button and text

    if (sortState === 'unsorted') {
        cards.sort((a, b) => new Date(b.querySelector('.upload-date').textContent) - new Date(a.querySelector('.upload-date').textContent));
        sortStatus.textContent = 'Newest to Oldest'; // Update sort status before changing the state
	sortStatus.style.visibility = 'visible';
        sortState = 'newest to oldest';
    } else if (sortState === 'newest to oldest') {
        cards.sort((a, b) => new Date(a.querySelector('.upload-date').textContent) - new Date(b.querySelector('.upload-date').textContent));
        sortStatus.textContent = 'Oldest to Newest'; // Update sort status before changing the state
        sortStatus.style.visibility = 'visible';
        sortState = 'oldest to newest';
    } else {
        cards = originalOrder.slice(); // Reset to the original order
        sortStatus.textContent = 'Unsorted'; // Update sort status before changing the state
        sortStatus.style.visibility = 'hidden'; 
        sortState = 'unsorted';
    }

   let sortStatusContainer = document.getElementById('sortStatusContainer');
    if (sortState !== 'unsorted') {
        sortStatus.textContent = sortState === 'newest to oldest' ? 'Newest to Oldest' : 'Oldest to Newest';
        sortStatusContainer.style.display = 'block'; // Show the sort status text
        sortContainer.classList.add('sort-active');

    } else {
        sortStatusContainer.style.display = 'none'; // Hide the sort status text
        sortContainer.classList.remove('sort-active');

    }

    // Rebuild the DOM with the sorted or original order cards
    cardsContainer.innerHTML = '';
    cards.forEach(card => cardsContainer.appendChild(card));

}





// When the DOM is fully loaded, set up the necessary event listeners and actions
document.addEventListener('DOMContentLoaded', function() {
    fetchAndDisplayNotes();
    equalCardSizes();

    // Fetch and display notes on page load
     fetchAndDisplayNotes().then(() => {
        originalOrder = Array.from(document.querySelector('.section-cards').children);
        //adjustContainerWidth();
        equalCardSizes();
    });
    




window.addEventListener('resize', function() {
    equalCardSizes(); 

});

    // Set up search functionality
document.getElementById('search-icon').addEventListener('click', function() {
        const searchTerm = document.querySelector('.search-bar input[type="text"]').value;
        if (searchTerm.trim() === '') {
        fetchAndDisplayNotes();
            return;
        }
        let formData = new FormData();
        formData.append('searchTerm', searchTerm);

        fetch('searchNotes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(notes => {
            updateNotesDisplay(notes);
            equalCardSizes();
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    });
});
</script>
</body>
</html>
