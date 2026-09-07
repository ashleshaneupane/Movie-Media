<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Media</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
        <h1 id="login-heading">LOGIN</h1>

    <form id="loginForm" action="">
<div class="form-groupLogin">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>
</div>

<div class="form-groupLogin">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
</div>
<a href="forgotPassword.php" class="forgotpw">Forgot Password?</a>
<button type="submit" class="login-btn">Login</button>
    <p class="paragraph">
    Not in our world? <a href="signup.php" class="signup"> SIGNUP</a> </p>
    </form>
    
</body>
</html>