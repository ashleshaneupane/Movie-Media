<?php

include 'includes/config.php';

$usernameServerError = "";
$emailServerError = "";
$signupServerError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // Check if username already exists

    $usernameCheck = $conn->prepare(
        "SELECT userID FROM Users WHERE username = ?"
    );

    $usernameCheck->bind_param("s", $username);
    $usernameCheck->execute();

    $usernameResult = $usernameCheck->get_result();


    if ($usernameResult->num_rows > 0) {

        $usernameServerError = "Username already exists.";

    } else {

        // Check if email already exists

        $emailCheck = $conn->prepare(
            "SELECT userID FROM Users WHERE email = ?"
        );

        $emailCheck->bind_param("s", $email);
        $emailCheck->execute();

        $emailResult = $emailCheck->get_result();


        if ($emailResult->num_rows > 0) {

            $emailServerError = "Email is already registered.";

        } else {

            // Hash the password

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // Insert new user

            $insertUser = $conn->prepare(
                "INSERT INTO Users (name, username, email, password)
                 VALUES (?, ?, ?, ?)"
            );

            $insertUser->bind_param(
                "ssss",
                $name,
                $username,
                $email,
                $hashedPassword
            );


            if ($insertUser->execute()) {

                header("Location: login.php?signup=success");
                exit;

            } else {

                $signupServerError =
                    "Something went wrong. Please try again.";

            }

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


<h1 id="signup-heading">
    SIGN UP
</h1>


<?php if ($signupServerError !== ""): ?>

    <p class="signup-server-error">
        <?php echo htmlspecialchars($signupServerError); ?>
    </p>

<?php endif; ?>


<form
    id="signupForm"
    method="POST"
    action="signup.php"
>


    <div class="form-group">

        <label for="name">
            Full Name
        </label>

        <input
            type="text"
            id="name"
            name="name"
            required
        >

        <small
            class="error"
            id="nameError"
        ></small>

    </div>


    <div class="form-group">

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            required
        >

        <small
            class="error"
            id="usernameError"
        >
            <?php echo htmlspecialchars($usernameServerError); ?>
        </small>

    </div>


    <div class="form-group">

        <label for="email">
            Email Address
        </label>

        <input
            type="email"
            id="email"
            name="email"
            required
        >

        <small
            class="error"
            id="emailError"
        >
            <?php echo htmlspecialchars($emailServerError); ?>
        </small>

    </div>


    <div class="form-group">

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <small
            class="error"
            id="passwordError"
        ></small>

    </div>


    <div class="form-group">

        <label for="confirmPassword">
            Confirm Password
        </label>

        <input
            type="password"
            id="confirmPassword"
            name="confirmPassword"
            required
        >

        <small
            class="error"
            id="confirmPasswordError"
        ></small>

    </div>


    <button
        type="submit"
        class="signup-btn"
    >
        Sign Up
    </button>


    <p class="paragraph">

        Already in our world?

        <a
            href="login.php"
            class="loginn"
        >
            LOGIN
        </a>

    </p>


</form>


<script src="js/signup.js"></script>

</body>

</html>