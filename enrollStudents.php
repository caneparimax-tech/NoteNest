<?php
session_start();
require(__DIR__ . "/connect.php");

$data = json_decode(file_get_contents('php://input'), true);
$students = $data['students'];

$response = ['success' => false];

try {
    $dbconnect->beginTransaction();

    foreach ($students as $student) {
        // Ensure the studentId and sectionId are integers
        $studentId = intval($student['studentId']);
        $sectionId = intval($student['sectionId']);

        // Check if the student is already enrolled in this section
        $checkStmt = $dbconnect->prepare("SELECT * FROM Enrollment WHERE SectionID = ? AND StudentID = ?");
        $checkStmt->execute([$sectionId, $studentId]);

        if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
            // Insert into Enrollment if not already enrolled
            $enrollStmt = $dbconnect->prepare("INSERT INTO Enrollment (SectionID, StudentID) VALUES (?, ?)");
            $enrollStmt->execute([$sectionId, $studentId]);
        }
    }

    $dbconnect->commit();
    $response['success'] = true;
} catch (Exception $e) {
    $dbconnect->rollBack();
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
