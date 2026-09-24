<?php 
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Setup database connection
require(__DIR__ . "/connect.php");

function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}


if (isAjaxRequest() && isset($_GET['action']) && $_GET['action'] === 'get_notes') {
    // JSON header for AJAX response
    header('Content-Type: application/json');
    
    $sectionID = $_GET['sectionID'] ?? null;
    if ($sectionID) {
    try {
         $stmt = $dbconnect->prepare("
            SELECT Notes.NoteID, Notes.NoteTitle, Notes.NoteDesc, Notes.UploadDate, Notes.Verified, Students.FName AS StudentFName, Students.LName AS StudentLName 
            FROM Notes 
            LEFT JOIN Students ON Notes.StudentID = Students.StudentID 
            WHERE Notes.SectionID = :sectionID
        ");
        $stmt->bindParam(':sectionID', $sectionID, PDO::PARAM_INT);
        $stmt->execute();
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($notes);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    } else {
        echo json_encode([]);
    }
    exit; // Stop script execution for AJAX requests
} else {

 $studentID = $_SESSION['StudentID'] ?? null;

    if (isset($_GET['courseID']) && isset($_GET['sectionID'])) {
        $courseID = $_GET['courseID'];
        $sectionID = $_GET['sectionID'];
        
        // Fetch course name etc.
	$stmt = $dbconnect->prepare("SELECT CourseName, CourseCode FROM Courses WHERE CourseID = :courseID");
	$stmt->bindParam(':courseID', $courseID, PDO::PARAM_INT);
	$stmt->execute();

	$course = $stmt->fetch(PDO::FETCH_ASSOC);

	$courseName = $course['CourseName'] ?? 'No course name';
	$courseCode = $course['CourseCode'] ?? 'No course code';
        
        // More non-AJAX page logic as needed...
    } else {
        header('Location: login.php');
        exit; 
    }



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
  padding-right: 20px; /* Space on the right inside the container */
  padding-bottom: 0px; /* Space on the right inside the container */

  margin-left: auto;
}
     .search-bar input[type="text"] {
  flex-grow: 1; /* Allows the input to grow and fill the space */
      margin-bottom: 0 !important;
  margin-right: 5px; /* Space between the input and the icon */
font-size: 14px;
      }
input[type="text"]:focus {
    outline: none;
}
     .search-icon {
      color: #fff;
      font-size: 30px;
      cursor: pointer;
      flex-shrink: 0;
      border-radius: 10px;
      padding: 4px;
    }
.search-icon:hover {
      color: #fff;
      font-size: 30px;
      cursor: pointer;
      flex-shrink: 0;
      background-color: white;
      color: red;
 
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
            min-width: 510px;
            width: max(700px, calc(80% - 250px)); /* Subtract the sidebar width */
            max-width: 95%;
    	    margin: 100px auto;
  	    box-sizing: border-box; /* Includes padding and border in the element's total width and height */
            position: relative; /* Position relative for absolute child positioning if needed */
  	    text-align: center;
            align-items: center;
            border-radius: 20px;
            padding: 20px; /* Add padding if needed */  
            padding-top: 30px;
overflow-x: hidden;
            
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
    margin: 0 auto; /* This will take care of horizontal centering */
    margin-top: 10px;
     /* Add some padding inside the .section-cards container */
     

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
    padding-top: 70px;
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

    .popup-container {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
  }
  /* Style for popup content */
  .popup-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 5px;
    text-align: left;
  }

  .popup-content label,
  .popup-content input {
    display: block;
    margin: 4px auto; /* Adding space between elements */
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
    color: #C8102E; /* Match the theme color or choose what fits */
    width: 180px;
    font-weight: bold;
}
#sortStatusContainer {
    height: 20px; /* Reserve space for one line of text */
    overflow: hidden; /* Prevent content from spilling outside the container */
    width: 180px;

}
.verified {
    color: green;
}

.unverified {
    color: red;
}

     </style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="topbar">
    <a href="studentUserHomepage.php" class="breadcrumb-link">Home </a><span class="arrow">></span>
    <span class="breadcrumb-current"><?php echo htmlspecialchars($courseName); ?> <?php echo htmlspecialchars($courseCode); ?></span>

    <div class="search-bar">
      <input type="text" placeholder="Search...">
      <i class="material-icons search-icon" id="search-icon">search</i>
    </div>
  </div>
    <div class="main-content">
        <div class="sidenav">
            <img src="NoteNest.png" alt="Logo" class="logo" width="300">
            <hr>
<ul>
    <li><a href= "studentUserHomepage.php" class="home"><i class="material-icons">home</i> Home</a></li>
    <li><a href="studentProfile.php" class="profile"><i class="material-icons">account_circle</i> Profile</a></li>
    <li><a href="logout.php" class="logout"><i class="material-icons">exit_to_app</i> Logout</a></li>
  </ul>
        </div>
       <div class="container">
    <div class="header-with-button">
    <div id="sortStatusContainer">
              <span id="sortStatus"></span>
         </div>

        <h2>Course Notes</h2>
        <button class="upload" onclick="openPopup()">Upload Notes</button>
    </div>

    <div class="sort-bar">
  <button class="sort-button" onclick="toggleSort()">Sort by Date</button>
</div>

<div id="popup" class="popup-container">
    <!-- Popup content -->
    <div class="popup-content">
      <span class="close-btn" onclick="closePopup()">&times;</span>
      <h2>Upload Notes</h2>
      <form action="upload.php" method="post" enctype="multipart/form-data">
      <label for="fileToUpload">Select PDF to upload:</label>
    <input type="file" name="fileToUpload" id="fileToUpload"><br>
    <label for="title">Title:</label>
    <input type="text" name="title" id="title"><br>
    <label for="description">Description:</label>
    <input type="text" name="noteDesc" id="noteDesc"><br>
    <!-- Hidden fields for courseID and sectionID -->
    <input type="hidden" name="courseID" value="<?php echo htmlspecialchars($courseID); ?>">
    <input type="hidden" name="sectionID" value="<?php echo htmlspecialchars($sectionID); ?>">
       <input type="hidden" name="studentID" value="<?php echo $studentID; ?>">
    <input type="submit" value="Upload" name="submit">
</form>
    </div>
  </div>

    <div class="section-cards" id="section-cards">
    <!-- Notes will be populated here by JavaScript -->
</div>
            
       

       </div>


<script>
var currentStudentId = <?php echo json_encode($_SESSION['StudentID']); ?>;

window.addEventListener('load', adjustCardSizes);
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
<a href="${note.StudentID == currentStudentId ? 'deleteViewNotes.php' : 'viewNotes.php'}?noteID=${note.NoteID}&title=${encodeURIComponent(note.NoteTitle)}&noteDesc=${encodeURIComponent(note.NoteDesc)}&date=${encodeURIComponent(note.UploadDate)}&author=${encodeURIComponent(note.FName + ' ' + note.LName)}&filePath=${encodeURIComponent(note.FilePath)}&verified=${encodeURIComponent(note.Verified ? 'Yes' : 'No')}" class="section-card-link">

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
// Function to fetch and display notes
function fetchAndDisplayNotes(sectionID) {
    console.log('Fetching notes for section:', sectionID);

    // Returning the fetch promise so that it can be used for chaining with `then`
    return fetch(`?action=get_notes&sectionID=${sectionID}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Server response:', response);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(notes => {
        console.log('Notes received:', notes);
        if (!Array.isArray(notes)) {
        throw new Error('Expected an array of notes');
             }
             // Check if the first note object has the expected structure
            if (notes.length > 0 && (!notes[0].hasOwnProperty('NoteTitle') || !notes[0].hasOwnProperty('NoteDesc'))) {
                  throw new Error('Notes do not have the expected properties');
             }

        const cardsContainer = document.getElementById('section-cards');
        cardsContainer.innerHTML = ''; // Clear existing notes

        notes.forEach(note => {

const uploadDate = new Date(note.UploadDate);
    
    // Format the date
    const formattedDate = uploadDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'numeric',
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
        adjustCardSizes();
    })
    .catch(error => {
        console.error('Error fetching notes:', error);
    });
}


function adjustCardSizes() {
  var cards = document.querySelectorAll('.section-card');
  var maxWidth = 0;
  var maxHeight = 0;

  // Find the widest and tallest card
  cards.forEach(function(card) {
    if (card.offsetWidth > maxWidth) {
      maxWidth = card.offsetWidth;
    }
    if (card.offsetHeight > maxHeight) {
      maxHeight = card.offsetHeight + (.05 * maxWidth);
    }
  });

  // Set all cards to the same width and height
  cards.forEach(function(card) {
    card.style.width = maxWidth + 'px';
    card.style.height = maxHeight + 'px';
  });

  var container = document.querySelector('.container');
  var minWidthPerCard = maxWidth; // Use the widest card width as the minimum width per card
  var currentCardsPerRow = Math.floor(container.offsetWidth / minWidthPerCard); // Calculate how many cards per row

  // Adjust the container width based on the number of cards
  var totalWidthNeeded = currentCardsPerRow * minWidthPerCard;
  container.style.width = totalWidthNeeded < container.offsetWidth ? totalWidthNeeded + 'px' : '100%';
}

// Call this function after fetching notes, uploading a new note, resizing the window, and after a search
document.addEventListener('DOMContentLoaded', adjustCardSizes);
window.addEventListener('resize', adjustCardSizes);


let sortState = 'unsorted'; // Initialize sortState at the top level, outside any function

function toggleSort() {
    const cardsContainer = document.querySelector('.section-cards');
    let cards = Array.from(cardsContainer.children);
    let sortStatus = document.getElementById('sortStatus'); // Get the sort status element

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

    if (sortState === 'unsorted') {
        sortStatus.style.display = 'none'; // Hide sort status when unsorted
    } else {
        sortStatus.style.display = 'block'; // Or 'inline', depending on your layout
    }

    // Rebuild the DOM with the sorted or original order cards
    cardsContainer.innerHTML = '';
    cards.forEach(card => cardsContainer.appendChild(card));

}





// Functions to show and hide the popup
function openPopup() {
    var popup = document.getElementById("popup");
    popup.style.display = "block";
}

function closePopup() {
    var popup = document.getElementById("popup");
    if(popup) {
        popup.style.display = "none";
    }
}

// When the DOM is fully loaded, set up the necessary event listeners and actions
document.addEventListener('DOMContentLoaded', function() {
    // Adjust the container width
    //adjustContainerWidth();
    adjustCardSizes();

    // Fetch and display notes on page load
     fetchAndDisplayNotes(<?php echo json_encode($sectionID); ?>).then(() => {
        originalOrder = Array.from(document.querySelector('.section-cards').children);
        //adjustContainerWidth();
        adjustCardSizes();
    });
    
    // Add event listener to the 'Upload Notes' button
    document.querySelector('.upload').addEventListener('click', openPopup);

    // Handle form submission via AJAX
    document.querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault();
        let formData = new FormData(this);
        formData.set('courseID', '<?php echo htmlspecialchars($courseID); ?>');
        formData.set('sectionID', '<?php echo htmlspecialchars($sectionID); ?>');
        formData.set('studentID', '<?php echo htmlspecialchars($studentID); ?>');


        fetch('upload.php', {
        method: 'POST',
        body: formData
    })

    .then(response => {
    if (!response.ok) {
        return response.text().then(text => Promise.reject(text));
    }
    return response.json(); 
})
    .then(data => {
        if (data.success) {
            
           fetchAndDisplayNotes(<?php echo json_encode($sectionID); ?>).then(() => {
                //adjustContainerWidth();
                adjustCardSizes();  
            });
            closePopup();
        } else {
            // Handle the error, perhaps display a message to the user
            console.error('Error uploading note:', data.error);
        }
    })
    .catch(error => {
        console.error('Error submitting form:', error);
    });
});

//window.addEventListener('load', adjustContainerWidth);
//window.addEventListener('resize', adjustContainerWidth);
window.addEventListener('resize', function() {
    adjustCardSizes(); 

  // Additionally, recalculate the container width on window resize
  var container = document.querySelector('.container');
  var cards = document.querySelectorAll('.section-card');
  var minWidthPerCard = cards.length > 0 ? cards[0].offsetWidth : 310; // If cards exist, use one's width, otherwise default to 310
  var currentCardsPerRow = Math.floor(container.parentElement.offsetWidth / minWidthPerCard); // Calculate how many cards per row fit in the parent width
  var totalWidthNeeded = currentCardsPerRow * minWidthPerCard; // Calculate total width needed

  // Set the new width of the container, but don't exceed the parent's width
  container.style.width = totalWidthNeeded < container.parentElement.offsetWidth ? totalWidthNeeded + 'px' : '100%';
});

    // Set up search functionality
document.getElementById('search-icon').addEventListener('click', function() {
        const searchTerm = document.querySelector('.search-bar input[type="text"]').value;
        if (searchTerm.trim() === '') {
        fetchAndDisplayNotes(<?php echo json_encode($sectionID); ?>);
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
            adjustCardSizes();
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    });
});
</script>
</body>
</html>
