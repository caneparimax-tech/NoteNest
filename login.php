
<?php
    session_start();
    require(__DIR__ . "/connect.php");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];
         try {
            $stmt = $dbconnect->prepare("SELECT UserID, Email, Password, Role FROM UserCredentials WHERE Email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

             if ($user && password_verify($password, $user['Password'])) {
      	         $_SESSION['Email'] = $user['Email'];
      	         $_SESSION['Role'] = $user['Role'];
    	         $_SESSION['UserID'] = $user['UserID']; // Assuming you've selected UserID in the query
              
   	     // Additional query to get StudentID
   	     if ($user['Role'] == 'Student') {
  	          $stmt = $dbconnect->prepare("SELECT StudentID FROM Students WHERE UserID = :userID");
  	          $stmt->bindParam(':userID', $user['UserID']);
  	          $stmt->execute();
  	          $student = $stmt->fetch(PDO::FETCH_ASSOC);
                  
   	      
   	          $_SESSION['StudentID'] = $student['StudentID'];
   	          header("Location: studentUserHomepage.php");
    	        
   	     } elseif ($user['Role'] == 'Instructor') {
   	         // Fetch and set InstructorID
    		$stmt = $dbconnect->prepare("SELECT InstructorID FROM Instructors WHERE UserID = :userID");
    		$stmt->bindParam(':userID', $user['UserID']);
    		$stmt->execute();
    		$instructor = $stmt->fetch(PDO::FETCH_ASSOC);

    		$_SESSION['InstructorID'] = $instructor['InstructorID'];
    		header("Location: instructorUserHomepage.php");
  	      } elseif ($user['Role'] == 'Admin') {
  	          // Handle admin login
  	          header("Location: adminUserHomepage.php");

              // This will get the AdminID and set it, I am not sure if we really ever need to use it though
            // $stmt = $dbconnect->prepare("SELECT AdminID FROM Admins WHERE UserID = :userID");
            // $stmt->bindParam(':userID', $user['UserID']);
            // $stmt->execute();
            // $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // $_SESSION['AdminID'] = $admin['AdminID'];
            // header("Location: adminUserHomepage.php");


   	     } else {
  	          header("Location: index.php");
   	     }
   	     exit();
 	   } else {
 	       //password is incorrect
 	       $message = "Incorrect email or password";
    	}
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    
    <div class="container">
        

        <div class="main-content">
            <h2>User Login</h2>
		<?php if (isset($_SESSION['success_message'])): ?>
    		    <script>
        		window.onload = function() {
            		    alert("<?php echo addslashes($_SESSION['success_message']); ?>");
        		};
    		    </script>
    		    <?php unset($_SESSION['success_message']); ?>
		<?php endif; ?>
	    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

            <label for="email"><b>Email</b></label>
            <input type="email" name="email" placeholder="Enter Email" id="email" required>

            <label for="password"><b>Password</b></label>
            <input type="password" name="password" placeholder="Enter Password" id="password" required>
                
            <?php if (isset($message)) { echo "<div class='error'>$message</div>"; } ?>
            <input type="submit" name="submit" value="Login">
            </form>
            <a href="register.php">Click Here to Create a New Account</a>
        </div>
    </div>

</body>
</html>
