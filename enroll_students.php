<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=mjcanepa', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);
$students = $data['students'];
$instructorId = $_SESSION['instructorId']; // Use the instructorId from the session

try {
    $pdo->beginTransaction();
    foreach ($students as $student) {
        // Find the sectionId for the given courseId and instructorId
        $sectionStmt = $pdo->prepare("SELECT SectionID FROM Sections WHERE CourseID = ? AND InstructorID = ?");
        $sectionStmt->execute([$student['courseId'], $instructorId]);
        $section = $sectionStmt->fetch(PDO::FETCH_ASSOC);

        if (!$section) {
            throw new Exception("No section found for the course and instructor provided.");
        }

        // Check if the student is already enrolled in this section
        $checkStmt = $pdo->prepare("SELECT * FROM Enrollment WHERE SectionID = ? AND StudentID = ?");
        $checkStmt->execute([$section['SectionID'], $student['studentId']]);
        if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
            continue; // Skip if already enrolled
        }

        // If not already enrolled, insert into Enrollment
        $stmt = $pdo->prepare("INSERT INTO Enrollment (SectionID, StudentID) VALUES (?, ?)");
        $stmt->execute([$section['SectionID'], $student['studentId']]);
    }
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
