<?php

session_start();

require(__DIR__ . "/connect.php");


// Get course ID from GET request
$courseId = $_GET['courseId'] ?? '';

if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'Course ID is required.']);
    exit;
}

// Wrap operations in a transaction for atomicity
$dbconnect->beginTransaction();

try {
    // Fetch all sections for the course
    $stmt_sections = $dbconnect->prepare("SELECT SectionID FROM Sections WHERE CourseID = :courseId");
    $stmt_sections->execute([':courseId' => $courseId]);
    $sections = $stmt_sections->fetchAll(PDO::FETCH_COLUMN);

    // Remove all enrollments for these sections
    foreach ($sections as $sectionId) {
        $stmt_unenroll = $dbconnect->prepare("DELETE FROM Enrollment WHERE SectionID = :sectionId");
        $stmt_unenroll->execute([':sectionId' => $sectionId]);
    }

    // Remove all sections
    $stmt_remove_sections = $dbconnect->prepare("DELETE FROM Sections WHERE CourseID = :courseId");
    $stmt_remove_sections->execute([':courseId' => $courseId]);

    // Finally, remove the course
    $stmt_remove_course = $dbconnect->prepare("DELETE FROM Courses WHERE CourseID = :courseId");
    $stmt_remove_course->execute([':courseId' => $courseId]);

    // If all operations were successful, commit the transaction
    $dbconnect->commit();

    // Echo result or confirmation
    echo json_encode(['success' => true, 'message' => 'Course and its sections removed successfully']);
} catch (PDOException $e) {
    // An error occurred, rollback any changes
    $dbconnect->rollBack();
    echo json_encode(['success' => false, 'message' => "Error removing course: " . $e->getMessage()]);
    exit;
}
?>
