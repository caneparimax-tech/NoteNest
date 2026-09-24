
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<style>
    .radio-container label,
    .radio-container input[type="radio"] {
        display: inline;
        margin-right: 5px; /* Adjust as needed */
        margin-bottom: 20px;
}
</style>
<body>
    <header>
        <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
    </header>
    <div class="container">
        <h2><b>Register</b></h2>
        <p>Please fill in this form to create an account</p>
        <hr>
        <form id="registrationForm" action="createAccount.php" method="post">
            <h3><b>Choose your account type:</b></h3>
            <div class="radio-container">
                <label for="student">Student</label>
                <input type="radio" name="accountType" id="student" value="Student" required>
                <label for="instructor">Instructor</label>
                <input type="radio" name="accountType" id="instructor" value="Instructor" required>
            </div>

            <label for="idNumber"><b>ID Number</b></label>
            <input type="text" name="idNumber" placeholder="Enter ID Number" id="idNumber" required oninput="validateIDNumber()">

            <label for="firstName"><b>First Name</b></label>
            <input type="text" name="firstName" placeholder="Enter First Name" id="firstName" required oninput="validateName(this)">

            <label for="lastName"><b>Last Name</b></label>
            <input type="text" name="lastName" placeholder="Enter Last Name" id="lastName" required oninput="validateName(this)">

            <label for="email"><b>Ole Miss Email</b></label>
            <input type="email" name="email" placeholder="Enter Email" id="email" required oninput="validateEmail()">

            <label for="password"><b>Password</b></label>
            <input type="password" name="password" placeholder="Enter Password" id="password" required oninput="validatePassword()">

            <label for="password2"><b>Confirm Password</b></label>
            <input type="password" name="password2" placeholder="Re-Enter Password" id="password2" required oninput="validatePasswordMatch()">

            <p>By creating an account you agree to our <a href="terms.php">Terms & Conditions</a></p>

            <input type="submit" name="submit" value="Register">
        </form> 
        <p>Already have an account?</p> 
        <p><a href="login.php">Log in here</a></p>
    </div>

<script>
        function validateIDNumber() {
            var idNumber = document.getElementById('idNumber').value;
            if (idNumber.length !== 8) {
                document.getElementById('idNumber').setCustomValidity('ID number must be exactly 8 characters.');
            } else {
                document.getElementById('idNumber').setCustomValidity('');
            }
        }

        function validateName(input) {
            var namePattern = /^[a-zA-Z.]{2,50}$/;
            if (!namePattern.test(input.value)) {
                input.setCustomValidity('Names must be 2-50 characters long and can only contain letters and periods.');
            } else {
                input.setCustomValidity('');
            }
        }

        function validateEmail() {
            var email = document.getElementById('email').value.toLowerCase();
            var accountType = document.querySelector('input[name="accountType"]:checked').value;
            if (accountType === 'Student' && !email.endsWith('@go.olemiss.edu')) {
                document.getElementById('email').setCustomValidity('Student email must end with @go.olemiss.edu.');
            } else if (accountType === 'Instructor' && !email.endsWith('@olemiss.edu')) {
                document.getElementById('email').setCustomValidity('Instructor email must end with @olemiss.edu.');
            } else {
                document.getElementById('email').setCustomValidity('');
            }
        }

        function validatePassword() {
            var password = document.getElementById('password').value;
            if (password.length < 8 || password.length > 25 || !/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[0-9]/.test(password)) {
                document.getElementById('password').setCustomValidity('Password must be 8-25 characters long and include at least one uppercase letter, one lowercase letter, and one number.');
            } else {
                document.getElementById('password').setCustomValidity('');
            }
        }

        function validatePasswordMatch() {
            var password = document.getElementById('password').value;
            var password2 = document.getElementById('password2').value;
            if (password !== password2) {
                document.getElementById('password2').setCustomValidity('Passwords do not match.');
            } else {
                document.getElementById('password2').setCustomValidity('');
            }
        }

        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            validateIDNumber();
            validateName(document.getElementById('firstName'));
            validateName(document.getElementById('lastName'));
            validateEmail();
            validatePassword();
            validatePasswordMatch();

            if (!this.checkValidity()) {
                event.preventDefault();
            }
        });
    </script>

</body>
</html>
