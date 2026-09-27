<?php

include 'includes/config.php';

$tokenError = "";
$passwordError = "";
$resetSuccess = false;

$token = $_GET["token"] ?? "";


if ($token === "") {

    $tokenError = "Invalid or missing reset link.";

} else {

    // Find valid token

    $tokenCheck = $conn->prepare(
        "SELECT resetID, userID
         FROM PasswordReset
         WHERE token = ?
         AND expiresAt > NOW()"
    );

    $tokenCheck->bind_param("s", $token);
    $tokenCheck->execute();

    $tokenResult = $tokenCheck->get_result();


    if ($tokenResult->num_rows === 0) {

        $tokenError =
            "This reset link is invalid or has expired.";

    } else {

        $resetData = $tokenResult->fetch_assoc();

        $userID = $resetData["userID"];


        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $newPassword = $_POST["newPassword"];
            $confirmPassword = $_POST["confirmPassword"];


            if (strlen($newPassword) < 8) {

                $passwordError =
                    "Password must be at least 8 characters.";

            } elseif ($newPassword !== $confirmPassword) {

                $passwordError =
                    "Passwords do not match.";

            } else {

                // Hash new password

                $hashedPassword = password_hash(
                    $newPassword,
                    PASSWORD_DEFAULT
                );


                // Update user's password

                $updatePassword = $conn->prepare(
                    "UPDATE Users
                     SET password = ?
                     WHERE userID = ?"
                );

                $updatePassword->bind_param(
                    "si",
                    $hashedPassword,
                    $userID
                );


                if ($updatePassword->execute()) {

                    // Delete used token

                    $deleteToken = $conn->prepare(
                        "DELETE FROM PasswordReset
                         WHERE resetID = ?"
                    );

                    $deleteToken->bind_param(
                        "i",
                        $resetData["resetID"]
                    );

                    $deleteToken->execute();


                    $resetSuccess = true;

                } else {

                    $passwordError =
                        "Something went wrong. Please try again.";

                }

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

    <title>Reset Password - Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>

<body>

<?php include 'includes/header.php'; ?>


<main class="reset-wrapper">

    <div class="reset-card">

        <?php if ($resetSuccess): ?>

            <h1 id="reset-heading">
                Password Updated
            </h1>

            <p class="subtitle">
                ✓ Your password has been changed successfully.
            </p>

            <p class="paragraph">

                <a
                    href="login.php"
                    class="back-link"
                >
                    Continue to Login
                </a>

            </p>


        <?php elseif ($tokenError !== ""): ?>

            <h1 id="reset-heading">
                Reset Link Invalid
            </h1>

            <p class="error">
                <?php echo htmlspecialchars($tokenError); ?>
            </p>

            <p class="paragraph">

                <a
                    href="forgotPassword.php"
                    class="back-link"
                >
                    Request a New Link
                </a>

            </p>


        <?php else: ?>

            <h1 id="reset-heading">
                Create New Password
            </h1>

            <p class="subtitle">
                Enter your new password below.
            </p>


            <?php if ($passwordError !== ""): ?>

                <p class="error">
                    <?php echo htmlspecialchars($passwordError); ?>
                </p>

            <?php endif; ?>


            <form
                id="resetForm"
                method="POST"
                action="resetPassword.php?token=<?php echo urlencode($token); ?>"
            >

                <div class="form-group">

                    <label for="newPassword">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="newPassword"
                        name="newPassword"
                        placeholder="Enter new password"
                        required
                    >

                    <span
                        class="error"
                        id="newPasswordError"
                    ></span>

                </div>


                <div class="form-group">

                    <label for="confirmPassword">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        id="confirmPassword"
                        name="confirmPassword"
                        placeholder="Confirm new password"
                        required
                    >

                    <span
                        class="error"
                        id="confirmPasswordError"
                    ></span>

                </div>


                <div class="button-container">

                    <button type="submit">
                        Reset Password
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

        <?php endif; ?>

    </div>

</main>

</body>

</html>