<?php 
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start or resume a session
session_start();

// Check if the user is logged in and has Admin role, if not, redirect to login page
//if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Instructor') {
    //header("Location: login.php");
    //exit();
//}

// Setup database connection
$pdo = new PDO('mysql:host=localhost;dbname=mjcanepa', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(isset($_GET['courseID']) && isset($_GET['sectionID'])) {
    
    $courseID = $_GET['courseID'];
    $sectionID = $_GET['sectionID'];
} else {
        header('Location: errorPage.php');
    exit; 
}

$stmt = $pdo->prepare("SELECT CourseName FROM Courses WHERE CourseID = :courseID");
$stmt->bindParam(':courseID', $courseID, PDO::PARAM_INT);
$stmt->execute();


    $courseName = $stmt->fetch(PDO::FETCH_ASSOC)['CourseName']; 

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
      width: calc(100%-250px); /* Set the width of the sidebar */
      z-index: 1; /* Stay on top */
      left: 250px;
      right: 0;
      background-color: #C8102E; 
                  position: fixed;
      overflow-x: hidden; /* Disable horizontal scroll */
      padding: 5px 20px;
      
      top: 50px; /* Adjust the top position to appear below the header */
      z-index: 998;
      display: flex; /* Use flexbox */
      justify-content: space-between;
      box-sizing: border-box;
      align-items: center;
        }
        .topbar span {
            font-size: 25px;
            color: #fff;
            display: block;
            padding: 4px 4px 4px 4px; 
            padding-left: 20px;
white-space: nowrap;
      overflow: visible;
      margin-right: auto;

        }
.search-bar {
  display: flex; /* Establishes a flex container */
  align-items: center; /* Vertically centers the flex items */
  padding-right: 20px; /* Space on the right inside the container */
}
     .search-bar input[type="text"] {
  flex-grow: 1; /* Allows the input to grow and fill the space */
      margin-bottom: 0 !important;
  margin-right: 10px; /* Space between the input and the icon */
      }
     .search-icon {
      color: #fff;
      font-size: 30px;
      cursor: pointer;
      flex-shrink: 0;
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
            min-width: calc(40% - 250px);
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
    margin-right: -150px;
 }

        .admin-button:hover {
            background-color: #002F6C !important;
            color: #fff;
            text-decoration: none;
        }
        .section-cards {
      width: calc(45% - 250px)
      max-width: calc(90% - 250px);
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* This will center the cards within the .section-cards container */
    align-items: flex-start; /* Align items to the start of the cross axis */
    margin: 0 auto; /* This will take care of horizontal centering */
    margin-top: 10px;
     /* Add some padding inside the .section-cards container */
     

        }
        .section-card {
            width: fit-content; /* Adjust card width as needed */
            min-width: 270px !important;
            background-color: #fff;
            border: 1px solid black;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow effect */
            margin: 20px;
            padding: 10px;
            max-width: 300px;
            display: flex;
            flex-direction: column;
    	    justify-content: center; /* This centers the content vertically */
    	    align-items: center;           
            position: relative;

}
.section-card h3 {
    margin-top: 5px; 
    
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
.section-card:hover .dropdown-content {
  display: block;
  opacity: 1; /* Make it visible on hover */
  visibility: visible; /* Show it */
  left: 0;
  transform: translateY(10px); /* Move back to its original position */
  position: relative !important;
  transition: opacity 0.3s, visibility 0.3s, transform 0.3s ease-out; 
transition: background-color 0.3s ease;
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

     </style>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="topbar">
    <span><?php echo htmlspecialchars($courseName); ?></span>
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
        <h2>Course Notes</h2>
        <button id="sort-button" onclick="toggleSort()">Sort by Date</button>
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
        <input type="text" name="description" id="description"><br>
        <input type="submit" value="Upload" name="submit">
      </form>
    </div>
  </div>

    <div class="section-cards">
        
           <?php for ($x = 1; $x < 10; $x++): ?>
  <div class="section-card">
                    <h3>Title: Note Title</h3>
                    <p>Publisher: Max Canepari</p>
                    <p>Description: ___________________</p>
                    <p>Upload Date: 4/6/2024</p>
                </div>   
    <!-- Dropdown Content -->
    <div class="dropdown">
      <div class="dropdown-content">
        <a href="#" class="remove-btn" onclick="removeSection(<?php echo htmlspecialchars($section['SectionID']); ?>);">Remove</a>
      </div>
    </div>
  
   <?php endfor; ?>

            
       

       </div>


<script>
function openPopup() {
  document.getElementById("popup").style.display = "block";
}

function closePopup() {
  document.getElementById("popup").style.display = "none";
}

function adjustContainerWidth() {
    var cards = document.querySelectorAll('.section-card');
    var container = document.querySelector('.container');
    var minWidthPerCard = 310; // Minimum width of a card plus margins
    
    // Define breakpoints for the number of cards per row
    var breakpoints = [3, 4, 7,]; // The upper limit for 1, 2, and 3 cards per row respectively
    var currentCardsPerRow = 1; // Start with 1 card per row

    // Determine the currentCardsPerRow based on the breakpoints
    for (var i = 0; i < breakpoints.length; i++) {
        if (cards.length > breakpoints[i]) {
            currentCardsPerRow = i + 2; // Increase the number of cards per row
        } else {
            break; // Stop if the number of cards is within the current breakpoint
        }
    }
    
    var totalWidthNeeded = currentCardsPerRow * minWidthPerCard; // Calculate total width needed

    // If there are fewer cards than the max for one row, we want to adjust the container width accordingly
    container.style.width = totalWidthNeeded + 'px';
    container.style.maxWidth = '90%';
}


// Trigger the function on page load and when the window is resized
window.onload = adjustContainerWidth;
window.onresize = adjustContainerWidth;


document.getElementById('search-icon').addEventListener('click', function() {
    const searchTerm = document.querySelector('.search-bar input[type="text"]').value;

    if (searchTerm.trim() === '') {
        displayAllNotes();
        return;
    }

    // Construct the FormData object to be sent with the fetch request
    let formData = new FormData();
    formData.append('searchTerm', searchTerm);

    // Perform the fetch request
    fetch('searchNotes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(notes => {
        updateNotesDisplay(notes);
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
});

function updateNotesDisplay(notes) {
    // Clear the current notes display
    const cardsContainer = document.querySelector('.section-cards');
    cardsContainer.innerHTML = '';

    // Add new notes to the display
    notes.forEach(note => {
        const cardHtml = `
            <div class="section-card">
                <h3>Title: ${note.NoteTitle}</h3>
                <p>Publisher: ${note.FName} ${note.LName}</p>
                <p>Description: ___________________</p> 
                <p>Upload Date: ${note.UploadDate}</p>
            </div>
        `;
        cardsContainer.innerHTML += cardHtml;
    });
}

function displayAllNotes() {
    // Make a request to a PHP script that retrieves all notes
    fetch('getAllNotes.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(notes => {
        updateNotesDisplay(notes);
    })
    .catch(error => {
        console.error('There was a problem fetching all notes:', error);
    });
}
let sortState = 'unsorted'; 
let originalOrder = []; 

function toggleSort() {
    const cardsContainer = document.querySelector('.section-cards');
    let cards = Array.from(cardsContainer.children);

    if (sortState === 'unsorted' || sortState === 'oldest') {
        // Sort from newest to oldest
        cards.sort((a, b) => new Date(b.querySelector('.upload-date').textContent) - new Date(a.querySelector('.upload-date').textContent));
        sortState = 'newest';
    } else if (sortState === 'newest') {
        // Sort from oldest to newest
        cards.sort((a, b) => new Date(a.querySelector('.upload-date').textContent) - new Date(b.querySelector('.upload-date').textContent));
        sortState = 'oldest';
    } else {
        // Restore original order
        cards = originalOrder.slice();
        sortState = 'unsorted';
    }

    // Update the DOM
    cardsContainer.innerHTML = '';
    cards.forEach(card => cardsContainer.appendChild(card));
}

window.onload = function() {
    const cardsContainer = document.querySelector('.section-cards');
    originalOrder = Array.from(cardsContainer.children); 
    adjustContainerWidth(); 
};

</script>
</body>
</html>

