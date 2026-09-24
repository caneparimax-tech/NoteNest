<?php
session_start();
ob_start();
require(__DIR__ . "/connect.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $error = false; // Initialize error variable
    $message = ''; // Initialize message variable

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['password'], $_POST['password2'], $_POST['firstName'], $_POST['lastName'], $_POST['accountType'], $_POST['idNumber'])) {
            $idNumber = $_POST['idNumber'];
            $email = strtolower($_POST['email']); // Convert email to lowercase for case-insensitive checks
            $accountType = $_POST['accountType'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $password = $_POST['password'];
            $password2 = $_POST['password2'];
            
            // Check if the passwords match
            if ($password !== $password2) {
                $error = true;
                $message = 'Passwords do not match.';
            }

            // ID Number check
            if (!$error && strlen($idNumber) != 8) {
                $error = true;
                $message = 'ID number must be exactly 8 characters.';
            }
            
            // Email domain checks
            if (!$error) {
                if ($accountType === 'Student' && !str_ends_with($email, '@go.olemiss.edu')) {
                    $error = true;
                    $message = 'Student email must end with @go.olemiss.edu.';
                } elseif ($accountType === 'Instructor' && !str_ends_with($email, '@olemiss.edu')) {
                    $error = true;
                    $message = 'Instructor email must end with @olemiss.edu.';
                } elseif ($accountType === 'Admin' && (!strpos($email, '@') || !strpos($email, '.'))) {
                    $error = true;
                    $message = 'Admin email must contain an @ and at least one period.';
                }
            }

            // Password checks
            if (!$error && (strlen($password) < 8 || strlen($password) > 25 || 
                !preg_match('/[A-Z]/', $password) ||
                !preg_match('/[a-z]/', $password) ||
                !preg_match('/[0-9]/', $password))) {
                $error = true;
                $message = 'Password must be 8-25 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
            }

            // Name checks
            $namePattern = "/^[a-zA-Z.]{2,50}$/";
            if (!$error && (!preg_match($namePattern, $firstName) || !preg_match($namePattern, $lastName))) {
                $error = true;
                $message = 'Names must be 2-50 characters long and can only contain letters and periods.';
            }

            if (!$error) {
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

                // Insert user credentials into UserCredentials table
                $insertUserCredentialsQuery = "INSERT INTO UserCredentials (Email, Password, Role) VALUES (:email, :password, :role)";
                $stmt = $dbconnect->prepare($insertUserCredentialsQuery);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':role', $accountType); // Ensure this line is added
                $stmt->execute();

                // Retrieve the UserID generated during insertion
                $userID = $dbconnect->lastInsertId();

                if (!$userID) {
                    // If $userID is not obtained from lastInsertId(), query for it explicitly
                    $getUserIDQuery = "SELECT UserID FROM UserCredentials WHERE Email = :email";
                    $stmt = $dbconnect->prepare($getUserIDQuery);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $userID = $row['UserID'];
                }

                // Insert additional user information based on account type
                $tableName = ucfirst($accountType) . "s";
                $idNumberColumn = ucfirst($accountType) . "ID";
                $insertUserInfoQuery = "INSERT INTO $tableName ($idNumberColumn, UserID, FName, LName) VALUES (:idNumber, :userID, :firstName, :lastName)";
                $stmt = $dbconnect->prepare($insertUserInfoQuery);
                $stmt->bindParam(':idNumber', $idNumber);
                $stmt->bindParam(':userID', $userID);
                $stmt->bindParam(':firstName', $firstName);
                $stmt->bindParam(':lastName', $lastName);
                $stmt->execute();
                
                if (!$stmt) {
            	     throw new Exception('Error executing statement: ' . implode(", ", $stmt->errorInfo()));
        	}
                
                
            }
            $_SESSION['success_message'] = 'Account successfully created. You can now log in.';
            header("Location: login.php");
            //header("Location: confirmation.php");
                exit();
        } else {
            $error = true;
            $message = 'All fields are required.';
        }
    

    // If there was an error, display it
    if ($error) {
        $_SESSION['error_message'] = $message;
        header("Location: register.php");
        exit();
    }
        }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo 'An unexpected error occurred. Please try again later.';
    header("Location: register.php");
    exit();
}

ob_end_flush();
?>

