<?php

session_start();

include 'includes/config.php';

$loginError = "";
$loginMessage = "";

if (isset($_GET["message"]) && $_GET["message"] === "login_required") {
    $loginMessage = "Please log in to access Movie Media.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];


    // Find user by username

    $userCheck = $conn->prepare(
        "SELECT userID, username, name, password, role
         FROM Users
         WHERE username = ?"
    );

    $userCheck->bind_param("s", $username);
    $userCheck->execute();

    $userResult = $userCheck->get_result();


    if ($userResult->num_rows === 0) {

        $loginError = "Incorrect username or password.";

    } else {

        $user = $userResult->fetch_assoc();


        // Verify password

        if (password_verify($password, $user["password"])) {

            // Create session

            $_SESSION["userID"] = $user["userID"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];


          // Login successful

if ($_SESSION["role"] === "admin") {
    header("Location: admin/index.php");
    exit;
}

header("Location: home.php");
exit;

        } else {

            $loginError = "Incorrect username or password.";

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>

<body>

<?php include 'includes/header.php'; ?>


<h1 id="login-heading">
    LOGIN
</h1>

<?php if ($loginMessage !== ""): ?>
    <p class="login-server-message">
        <?php echo htmlspecialchars($loginMessage); ?>
    </p>
<?php endif; ?>
<?php if ($loginError !== ""): ?>

    <p class="login-server-error">
        <?php echo htmlspecialchars($loginError); ?>
    </p>

<?php endif; ?>


<form
    id="loginForm"
    method="POST"
    action="login.php"
>


    <div class="form-groupLogin">

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            required
        >

    </div>


    <div class="form-groupLogin">

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

    </div>


    <a
        href="forgotPassword.php"
        class="forgotpw"
    >
        Forgot Password?
    </a>


    <button
        type="submit"
        class="login-btn"
    >
        Login
    </button>


    <p class="paragraph">

        Not in our world?

        <a
            href="signup.php"
            class="signup"
        >
            SIGNUP
        </a>

    </p>


</form>

</body>

</html>