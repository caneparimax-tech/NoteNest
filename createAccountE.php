
<?php
require(__DIR__ . "/connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from the registration form
    if (isset($_POST['email'], $_POST['password'], $_POST['password2'], $_POST['firstName'], $_POST['lastName'], $_POST['role'], $_POST['idNumber'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password2 = $_POST['password2']; 
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $role = $_POST['role'];
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

                // Insert user credentials into UserCredentials table
                $insertUserCredentialsQuery = "INSERT INTO UserCredentials (Email, Password, Role) VALUES (:email, :password, :role)";
                $stmt = $dbconnect->prepare($insertUserCredentialsQuery);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':role', $role);
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
                $tableName = ucfirst($role) . "s";
                $idNumberColumn = ucfirst($role) . "ID";
                $insertUserInfoQuery = "INSERT INTO $tableName ($idNumberColumn, UserID, FName, LName) VALUES (:idNumber, :userID, :firstName, :lastName)";
                $stmt = $dbconnect->prepare($insertUserInfoQuery);
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
