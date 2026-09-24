<?php
session_start();

// Set up the PDO connection
$pdo = new PDO('mysql:host=localhost;dbname=mjcanepa', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get the courseId from the POST request
$courseId = $_POST['courseId'] ?? null; // Using null coalescing operator for PHP 7 or later
$response = ['success' => false];

if ($courseId) {
    // Prepare the SQL statement
    $query = "SELECT DISTINCT i.FName, i.LName 
              FROM Instructors i
              JOIN Sections s ON i.InstructorID = s.InstructorID
              WHERE s.CourseID = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$courseId]);
    $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($instructors) {
        $response['success'] = true;
        // Map instructors to a string with first and last name
        $response['instructors'] = array_map(function($instructor) {
            return $instructor['FName'] . ' ' . $instructor['LName'];
        }, $instructors);
    } else {
        $response['error'] = "No instructors found for the selected course.";
    }
} else {
    $response['error'] = 'Course ID not provided.';
}

// Specify the content type to be JSON
header('Content-Type: application/json');
// Send back the JSON response
echo json_encode($response);
?>