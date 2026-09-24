<?php

session_start();

require(__DIR__ . "/connect.php");


$sectionId = $_POST['sectionId'];


$dbconnect->beginTransaction();

try {
    // First, remove all enrollments for this section
    $stmt_unenroll = $dbconnect->prepare("DELETE FROM Enrollment WHERE SectionID = :sectionId");
    $stmt_unenroll->execute([':sectionId' => $sectionId]);

    // Then, remove the section itself
    $stmt_remove_section = $dbconnect->prepare("DELETE FROM Sections WHERE SectionID = :sectionId");
    $stmt_remove_section->execute([':sectionId' => $sectionId]);

    // If both operations were successful, commit the transaction
    $dbconnect->commit();
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // An error occurred, rollback any changes
    $dbconnect->rollBack();

    // Return a JSON response indicating failure and include the error message
    echo json_encode(['success' => false, 'message' => "Error removing section: " . $e->getMessage()]);
}
?>