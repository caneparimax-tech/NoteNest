<?php
session_start();
require(__DIR__ . "/connect.php");

$sectionId = $_POST['sectionID']; // Get the sectionID from the request
$studentId = $_POST['studentId'];

$response = array('success' => false);

if($sectionId && $studentId) {
    // Query to get the student's name based on the studentId
    $query = "SELECT FName, LName FROM Students WHERE StudentID = ?";
    $stmt = $dbconnect->prepare($query);
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if($student) {
        $response['success'] = true;
        $response['studentId'] = $studentId;
        $response['studentName'] = $student['FName'] . ' ' . $student['LName'];
    } else {
        $response['error'] = 'No student found with the provided ID.';
    }
} else {
    $response['error'] = 'Missing section or student ID.'; // Updated error message
}

header('Content-Type: application/json');
echo json_encode($response);
?>