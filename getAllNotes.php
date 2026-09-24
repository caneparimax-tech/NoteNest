<?php
require(__DIR__ . "/connect.php");

$stmt = $dbconnect->prepare("
    SELECT n.NoteTitle, s.FName, s.LName, n.UploadDate
    FROM Notes n
    JOIN Students s ON n.StudentID = s.StudentID
");
$stmt->execute();
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode($notes);
?>
