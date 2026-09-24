<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=mjcanepa', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$courseId = $_POST['courseId'];
$studentId = $_POST['studentId'];

$response = array('success' => false);

if($courseId && $studentId) {
    // Query to get the student's name based on the studentId
    $query = "SELECT FName, LName FROM Students WHERE StudentID = ?";
    $stmt = $pdo->prepare($query);
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
    $response['error'] = 'Missing course or student ID.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>