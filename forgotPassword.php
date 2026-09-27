<?php

include 'includes/config.php';

$emailError = "";
$resetMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);


    if ($email === "") {

        $emailError = "Email address is required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $emailError = "Please enter a valid email address.";

    } else {

        // Find user by email

        $userCheck = $conn->prepare(
            "SELECT userID FROM Users WHERE email = ?"
        );

        $userCheck->bind_param("s", $email);
        $userCheck->execute();

        $userResult = $userCheck->get_result();


        if ($userResult->num_rows === 0) {

            $emailError = "No account was found with that email address.";

        } else {

            $user = $userResult->fetch_assoc();

            $userID = $user["userID"];


            // Create random reset token

            $token = bin2hex(random_bytes(32));

            // Token expires after 30 minutes

            $expiresAt = date(
                "Y-m-d H:i:s",
                time() + (30 * 60)
            );



            // Remove old reset tokens for this user

            $deleteOld = $conn->prepare(
                "DELETE FROM PasswordReset WHERE userID = ?"
            );

            $deleteOld->bind_param("i", $userID);
            $deleteOld->execute();


            // Store new token

            $insertToken = $conn->prepare(
                "INSERT INTO PasswordReset
                (userID, token, expiresAt)
                VALUES (?, ?, ?)"
            );

            $insertToken->bind_param(
                "iss",
                $userID,
                $token,
                $expiresAt
            );


            if ($insertToken->execute()) {

    header(
        "Location: resetPassword.php?token=" .
        urlencode($token)
    );

    exit;

} else {

                $emailError =
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

    <title>Forgot Password - Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>

<body>

<?php include 'includes/header.php'; ?>


<main class="forgot-wrapper">

    <div class="forgot-card">

        <h1 id="forgot-heading">
            Reset Password
        </h1>


        <p class="subtitle">
            Enter the email that you used to create your account.
        </p>


        <?php if ($emailError !== ""): ?>

            <span class="error forgot-server-error">
                <?php echo htmlspecialchars($emailError); ?>
            </span>

        <?php endif; ?>



        <form
            id="forgotForm"
            method="POST"
            action="forgotPassword.php"
        >

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

                <span
                    class="error"
                    id="emailError"
                ></span>

            </div>


            <div class="button-container">

                <button type="submit">
                    Send Reset Link
                </button>

            </div>

        </form>


        <p class="paragraph">

            <a
                href="login.php"
                class="back-link"
            >
                &larr; Back to Login
            </a>

        </p>

    </div>

</main>

</body>

</html>