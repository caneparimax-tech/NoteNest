<?php
session_start();

// Set up the PDO connection
require(__DIR__ . "/connect.php");

// Get the courseId from the POST request
$courseId = $_POST['courseId'] ?? null; // Using null coalescing operator for PHP 7 or later
$response = ['success' => false];

if ($courseId) {
    // Prepare the SQL statement to get instructors and their corresponding section IDs
    $query = "SELECT DISTINCT i.FName, i.LName, s.SectionID 
              FROM Instructors i
              JOIN Sections s ON i.InstructorID = s.InstructorID
              WHERE s.CourseID = ?";
    $stmt = $dbconnect->prepare($query);
    $stmt->execute([$courseId]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sections) {
        $response['success'] = true;
        $response['sections'] = array_map(function($section) {
            // Combine the instructor's name and include the sectionID
            return [
                'instructorName' => $section['FName'] . ' ' . $section['LName'],
                'sectionID' => $section['SectionID']
            ];
        }, $sections);
    } else {
        $response['error'] = "No sections found for the selected course.";
    }
} else {
    $response['error'] = 'Course ID not provided.';
}

// Specify the content type to be JSON
header('Content-Type: application/json');
// Send back the JSON response
echo json_encode($response);
?>
