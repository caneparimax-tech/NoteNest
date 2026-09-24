<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=mjcanepa', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);
$instructorName = $data['instructorName'];
$students = $data['students'];

try {
    $pdo->beginTransaction();

    // Split the instructor name into first and last names
    list($fName, $lName) = explode(' ', $instructorName, 2);

    // Find the SectionID for the given instructor's name
    $sectionStmt = $pdo->prepare("SELECT s.SectionID FROM Sections s
                                  JOIN Instructors i ON s.InstructorID = i.InstructorID
                                  WHERE i.FName = ? AND i.LName = ? LIMIT 1");
    $sectionStmt->execute([$fName, $lName]);
    $section = $sectionStmt->fetch(PDO::FETCH_ASSOC);

    if (!$section) {
        throw new Exception('No section found for the provided instructor name.');
    }
    $sectionId = $section['SectionID'];

    foreach ($students as $student) {
        // Check if the student is already enrolled in this section
        $checkStmt = $pdo->prepare("SELECT * FROM Enrollment WHERE SectionID = ? AND StudentID = ?");
        $checkStmt->execute([$sectionId, $student['studentId']]);
        if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
            continue; // Skip if already enrolled
        }

        // Insert into Enrollment
        $enrollStmt = $pdo->prepare("INSERT INTO Enrollment (SectionID, StudentID) VALUES (?, ?)");
        $enrollStmt->execute([$sectionId, $student['studentId']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>