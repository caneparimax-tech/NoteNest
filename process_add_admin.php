<?php
session_start();

// Check if user is logged in and is an admin, if not redirect to login page
if (!isset($_SESSION['Email']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require(__DIR__ . "/connect.php");

    if (isset($_POST['email'], $_POST['password'], $_POST['password2'], $_POST['firstName'], $_POST['lastName'], $_POST['idNumber'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password2 = $_POST['password2']; 
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $idNumber = $_POST['idNumber'];

        // Check if the passwords match
        if ($password != $password2) {
            $error = true;
            $message = 'Passwords do not match';
        } else {
            // Check if the email is already registered
            $checkEmailQuery = "SELECT Email FROM UserCredentials WHERE Email = :email";
            $stmt = $dbconnect->prepare($checkEmailQuery);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetchColumn()) {
                $error = true;
                $message = 'Email already in use';
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert admin credentials into UserCredentials table
                $insertAdminCredentialsQuery = "INSERT INTO UserCredentials (Email, Password, Role) VALUES (:email, :password, 'Admin')";
                $stmt = $dbconnect->prepare($insertAdminCredentialsQuery);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->execute();

                // Retrieve the UserID generated during insertion
                $userID = $dbconnect->lastInsertId();

                // Insert additional admin information
                $insertAdminInfoQuery = "INSERT INTO Admins (AdminID, UserID, FName, LName) VALUES (:idNumber, :userID, :firstName, :lastName)";
                $stmt = $dbconnect->prepare($insertAdminInfoQuery);
                $stmt->bindParam(':idNumber', $idNumber);
                $stmt->bindParam(':userID', $userID);
                $stmt->bindParam(':firstName', $firstName);
                $stmt->bindParam(':lastName', $lastName);
                $stmt->execute();

                // Redirect the user to a confirmation page
                header("Location: confirmation.php");
                exit();
            }
        }
    } else {
        $error = true;
        $message = 'All fields are required';
    }
}
?>