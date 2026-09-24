<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <title>NoteNest</title>
  <style>
    body {
      background-image: url('trees.jpeg');
      background-size: cover;
    }
    .welcome-message {
      font-family: "Georgia", serif;
      color: #ffffff;
      text-shadow: 2px 4px 2px #002F6C;
      text-align: center;
      transform: translateY(110%);
    }

    @media screen and (min-width: 951px) {
      .welcome-message {
        padding-bottom: -7%;
        font-size: 50px;
      }
    }
    /* If the screen size is 600px wide or less, set the font-size of <div> to 30px */
    @media screen and (max-width: 950px) {
      .welcome-message {
        padding-bottom: 7%;
        font-size: 40px;
      }
    }
    .container {
      width: 45%;
      height: 50%;
      margin: 100px auto;
      text-align: center;
      border-radius: 20px;
    }
    .logo {  
      width: 70%;
    }
    .btn {
      display: inline-block;
      padding: 2% 3%;
      margin: 3%;
      text-decoration: none;
      color: #fff;
      background-color: #C8102E;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .btn:hover {
      background-color: #002F6C;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <header>
    <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">University of Mississippi</h1>
  </header>
  <div class="welcome-message">
    <span class="welcome-text"><b>Welcome to</b></span>
  </div>  
  <div class="container">
    <img src="NoteNest.png" alt="Logo" class="logo" width="150">
    <div>
      <a href="aboutUs.php" class="btn">ABOUT US</a>
      <a href="register.php" class="btn">REGISTER</a>
      <a href="login.php" class="btn">LOGIN</a>
    </div>
  </div>
</body>
</html>