<?php
session_start();

if (!isset($_SESSION['Email'])) {
    header("Location: login.php");
    exit();
}

require(__DIR__ . "/connect.php");

$email = $_SESSION['Email'];

try {
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbconnect->beginTransaction();

    // Retrieve UserID from UserCredentials based on session email
    $stmt = $dbconnect->prepare("SELECT UserID FROM UserCredentials WHERE Email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userID = $user['UserID'];

        // Delete user from Students
        $deleteStudent = $dbconnect->prepare("DELETE FROM Students WHERE UserID = :userid");
        $deleteStudent->bindParam(':userid', $userID);
        $deleteStudent->execute();

        // Delete user from UserCredentials 
        $deleteUser = $dbconnect->prepare("DELETE FROM UserCredentials WHERE UserID = :userid");
        $deleteUser->bindParam(':userid', $userID);
        $deleteUser->execute();

        $dbconnect->commit();
        session_destroy();
        echo "Account deleted successfully.";
    } else {
        echo "No user found with the specified email address.";
    }
} catch(PDOException $e) {
    $dbconnect->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
