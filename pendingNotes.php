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

function getInstructorID() {
    if (isset($_SESSION['InstructorID']) && !empty($_SESSION['InstructorID'])) {
        return $_SESSION['InstructorID'];
    } else {
        throw new Exception('InstructorID not set in session');
    }
}

if (isAjaxRequest() && isset($_GET['action']) && $_GET['action'] === 'get_pending_notes') {
    header('Content-Type: application/json');
    
    $instructorID = getInstructorID();

$query = "
    SELECT 
        Notes.NoteID,
        Notes.NoteTitle,
        Notes.NoteDesc,
        Notes.UploadDate,
        Notes.Verified,
        Sections.SectionID,
        Sections.InstructorID,
        Students.StudentID,
        Students.FName AS StudentFName,
        Students.LName AS StudentLName,
        Courses.CourseCode 
    FROM 
        Notes 
    JOIN 
        Sections ON Notes.SectionID = Sections.SectionID 
    JOIN 
        Courses ON Sections.CourseID = Courses.CourseID 
    LEFT JOIN 
        Students ON Notes.StudentID = Students.StudentID 
    WHERE 
        Sections.InstructorID = :instructorID AND 
        Notes.Verified = 0
";
    try {
        $stmt = $dbconnect->prepare($query);
        $stmt->bindParam(':instructorID', $instructorID, PDO::PARAM_INT);
        $stmt->execute();
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
error_log(print_r($notes, true)); 
        echo json_encode(['status' => 'Success', 'notes' => $notes]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
    exit;
} else {
    // Redirect to the login page if not logged in as an instructor
    if (!isset($_SESSION['InstructorID'])) {
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

    <title>Pending Notes</title>
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
        .sidenav a.pendingNotes { 
            background-color: #e3eef4;
            color: #002F6C;
            border-radius: 2px;
            border-left: 4px solid #002F6C;
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
            margin-left: -180px;
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
        .section-card:hover {
        opacity: 1; /* Make it visible on hover */
        visibility: visible; /* Show it */
        background-color: grey;
        transition: background-color 0.3s ease;
            background-color: #F0F0F0;
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
        .sort-button:hover{
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
        .course-name {
            font-weight: bold;
            color: black; /* Default for unverified, could be dynamic as per JS */
        }

     </style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="topbar">
        <span> Pending Notes </span>

    <div class="search-bar">
      <input type="text" placeholder="Search...">
      <i class="material-icons search-icon" id="search-icon">search</i>
    </div>
  </div>
    <div class="main-content">
        <div class="sidenav">
            <img src="NoteNest.png" alt="Logo" class="logo" width="300">
            <hr>
            <a href="instructorUserHomepage.php" class="home"><i class="material-icons">home</i></i> Home</a>
            <a href="instructorProfile.php" class="profile"><i class="material-icons">account_circle</i></i> Profile</a>
            <a href="pendingNotes.php" class="pendingNotes"><i class="material-icons">pending_actions</i></i> Pending Notes</a> 
            <a href="instructorEnrollment.php" class="enrollment"><i class="material-icons">group_add</i></i> Enrollment</a> 
            <a href="logout.php" class="logout"><i class="material-icons">logout</i></i> Logout</a>
          </div>
       <div class="container">
    <div class="header-with-button">
    <div id="sortStatusContainer">
              <span id="sortStatus"></span>
         </div>

        <h2>Pending Notes</h2>
    </div>

    <div class="sort-bar">
  <button class="sort-button" onclick="toggleSort()">Sort by Date</button>
</div>


    <div class="section-cards" id="section-cards">
    <!-- Notes will be populated here by JavaScript -->
</div>
            
       

       </div>

<script>
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
            const publisher = note.StudentFName && note.StudentLName ? `${note.StudentFName} ${note.StudentLName}` : 'Unknown Publisher';

            cardsContainer.innerHTML += `
<a href="verifyViewNotes.php?noteID=${encodeURIComponent(note.NoteID)}&title=${encodeURIComponent(note.NoteTitle)}&noteDesc=${encodeURIComponent(note.NoteDesc)}&date=${encodeURIComponent(formattedDate)}&verified=${note.Verified ? 'Yes' : 'No'}&author=${encodeURIComponent(publisher)}&filePath=${encodeURIComponent(note.FilePath)}" class="section-card-link">
    <div class="section-card">
        <h3>Title: ${note.NoteTitle}</h3>
        <p>Author: ${publisher}</p>
        <p class="course-name">Course: ${note.CourseCode}</p>
        <p class="upload-date">Upload Date: ${formattedDate}</p>
    </div>
</a>
            `;
        });
    }
}
// Function to fetch and display notes
function fetchAndDisplayNotes() {
    console.log('Fetching pending notes for the current instructor');

    return fetch(`?action=get_pending_notes`, {
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
    .then(data => {
        console.log('Notes received:', data);
        if (data.error) {
            throw new Error(data.error);
        }
        if (!Array.isArray(data.notes)) {
            throw new Error('Expected an array of notes');
        }

        const cardsContainer = document.getElementById('section-cards');
        cardsContainer.innerHTML = ''; // Clear existing notes

        data.notes.forEach(note => {
            const formattedDate = new Date(note.UploadDate).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric'
            });
            const publisher = note.StudentFName && note.StudentLName ? `${note.StudentFName} ${note.StudentLName}` : 'Unknown Publisher';

           cardsContainer.innerHTML += `
<a href="verifyViewNotes.php?noteID=${encodeURIComponent(note.NoteID)}&title=${encodeURIComponent(note.NoteTitle)}&noteDesc=${encodeURIComponent(note.NoteDesc)}&date=${encodeURIComponent(formattedDate)}&verified=${note.Verified ? 'Yes' : 'No'}&author=${encodeURIComponent(publisher)}&filePath=${encodeURIComponent(note.FilePath)}" class="section-card-link">
    <div class="section-card">
        <h3>Title: ${note.NoteTitle}</h3>
        <p>Author: ${publisher}</p>
        <p class="course-name">Course: ${note.CourseCode}</p>
        <p class="upload-date">Upload Date: ${formattedDate}</p>
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

// Function to adjust container width based on the number of cards
// function adjustContainerWidth() {
//     var cards = document.querySelectorAll('.section-card');
//     var container = document.querySelector('.container');
//     var minWidthPerCard = 310; // Minimum width of a card plus margins
//     // Define breakpoints for the number of cards per row
//     var breakpoints = [3, 4, 7]; // The upper limit for 1, 2, and 3 cards per row respectively
//     var currentCardsPerRow = 1; // Start with 1 card per row
//     // Determine the currentCardsPerRow based on the breakpoints
//     for (var i = 0; i < breakpoints.length; i++) {
//         if (cards.length > breakpoints[i]) {
//             currentCardsPerRow = i + 2; // Increase the number of cards per row
//         } else {
//             break; // Stop if the number of cards is within the current breakpoint
//         }
//     }
//     var totalWidthNeeded = currentCardsPerRow * minWidthPerCard; // Calculate total width needed
//     // If there are fewer cards than the max for one row, adjust the container width accordingly
//     container.style.width = totalWidthNeeded + 'px';
//     container.style.maxWidth = '90%';
// }

function adjustCardSizes() {
  var cards = document.querySelectorAll('.section-card');
  var maxWidth = 0;
  var maxHeight = 0;

 cards.forEach(function(card) {
    card.style.height = 'auto';
  });

  // Find the widest and tallest card
  cards.forEach(function(card) {
    if (card.offsetWidth > maxWidth) {
      maxWidth = card.offsetWidth;
    }
    if (card.offsetHeight > maxHeight) {
      maxHeight = card.offsetHeight;
    }
  });
  
  var proportionalHeight = maxHeight + (0.05 * maxWidth);

  // Set all cards to the same width and height
  cards.forEach(function(card) {
    card.style.width = maxWidth + 'px';
    card.style.height = proportionalHeight + 'px';
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






// When the DOM is fully loaded, set up the necessary event listeners and actions
document.addEventListener('DOMContentLoaded', function() {
    // Adjust the container width
    //adjustContainerWidth();
    adjustCardSizes();

    // Fetch and display notes on page load
     fetchAndDisplayNotes().then(() => {
        originalOrder = Array.from(document.querySelector('.section-cards').children);
        //adjustContainerWidth();
        adjustCardSizes();
    });
    
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
