<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Media</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <nav>
        <img class="logo" src="images/logo.png" alt="logo">
        <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="search.php">Search</a>
        <a href="connect.php">Connect</a>
        <a href="notifications.php">Notifications</a>
        <a href="account.php">Account</a>
        </div>
    </nav>
    <h1 id="signup-heading">SIGN UP</h1>

    <form id="signupForm" action="">
    <div class="form-group">
    <label for="name">Full Name</label>
    <input type="text" id="name" name="name" required>
</div>

<div class="form-group">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>
</div>

<div class="form-group">
    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" required>
</div>

<div class="form-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
</div>

<div class="form-group">
    <label for="confirmPassword">Confirm Password</label>
    <input type="password" id="confirmPassword" name="confirmPassword" required>
</div>
<button type="submit" class="signup-btn">Sign Up</button>
    <p class="paragraph">
    Already in our world? <a href="login.php" class="login"> LOGIN</a> </p>
    </form>
    <script src="js/signup.js"></script>
</body>
</html>