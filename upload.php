<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require(__DIR__ . "/connect.php");

try {
    // Validate file upload
    if ($_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
        // Handle file upload error
        //header("Location: error.php");
        exit();
    }

   // var_dump($_FILES['fileToUpload']);

    // Get the uploaded file details
    $fileName = basename($_FILES['fileToUpload']['name']);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $fileSize = $_FILES['fileToUpload']['size'];

    // Debugging: Print file details
    echo "File Name: " . $fileName . "<br>";
    echo "File Type: " . $fileType . "<br>";
    echo "File Size: " . $fileSize . " bytes<br>";


    // Check file size
    if ($fileSize > 5000000) { // 5MB
        // Handle file size error
        //header("Location: error.php");
        exit();
    }

    // Check file type
    $allowedTypes = array('pdf'); // Add more file types if needed
    if (!in_array($fileType, $allowedTypes)) {
        // Handle file type error
        //header("Location: error.php");
        exit();
    }

    // Directory where uploaded files will be saved
    $uploadBaseDir = '/home/group11/public_html/Notes/';
    $courseID = $_REQUEST['courseID'];
    $sectionID = $_REQUEST['sectionID'];

    $courseDir = $uploadBaseDir . 'course_' . $courseID . '/';
    $sectionDir = $courseDir . 'section_' . $sectionID . '/';

    // Create directory if it doesn't exist
    if (!is_dir($courseDir)) {
        if (!mkdir($courseDir, 0757, true)) { // Change permission to 0755
            // Directory creation failed, log error and handle
            error_log("Failed to create directory: $courseDir", 0);
            // Handle error, maybe show user a message
            exit("Failed to create directory: $courseDir");
        }
    }

    // Check if section directory exists or create it
    if (!is_dir($sectionDir)) {
        if (!mkdir($sectionDir, 0757, true)) { // Change permission to 0755
            // Directory creation failed, log error and handle
            error_log("Failed to create directory: $sectionDir", 0);
            // Handle error, maybe show user a message
            exit("Failed to create directory: $sectionDir");
        }
    }

    // Generate a unique file name
    $filePath = $sectionDir . $fileName;

    // Move the uploaded file to the desired directory
    if (!move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $filePath)) {
        // Handle file move error
        //header("Location: error.php");
        exit();
    }

    // Set permissions for uploaded file
    chmod($filePath, 0644); // Change permission to 0644

    // Get other form data
    $noteTitle = $_POST['title'];
    $noteDesc = $_POST['description'];
    $studentID = $_SESSION['StudentID'] ?? null;

    // Insert record into the database
    $sql = "INSERT INTO Notes (NoteTitle, NoteDesc, FilePath, SectionID, StudentID) 
            VALUES (:noteTitle, :noteDesc, :filePath, :sectionID, :studentID)";
    $stmt = $dbconnect->prepare($sql);
    $stmt->bindParam(':noteTitle', $noteTitle);
    $stmt->bindParam(':noteDesc', $noteDesc);
    $stmt->bindParam(':filePath', $filePath);
    $stmt->bindParam(':sectionID', $sectionID);
    $stmt->bindParam(':studentID', $studentID);
    $stmt->execute();

    $_SESSION['noteUploadSuccess'] = true;

    // Redirect to a success page
    header("Location: TESTstudentSectionHomepage.php?uploadSuccess=1&courseID={$courseID}&sectionID={$sectionID}");
    exit();

} catch (PDOException $e) {
    // Handle database error
    echo "Database Error: " . $e->getMessage();
} 
?>
