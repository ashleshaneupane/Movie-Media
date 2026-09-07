<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <main class="reset-wrapper">

        <div class="reset-card">

            <h1 id="reset-heading">Create New Password</h1>

            <p class="subtitle">
                Enter your new password below.
            </p>

            <form id="resetForm">

                <div class="form-group">

                    <label for="newPassword">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="newPassword"
                        name="newPassword"
                        placeholder="Enter new password"
                    >

                    <span class="error" id="newPasswordError"></span>

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
                    >

                    <span class="error" id="confirmPasswordError"></span>

                </div>

                <div class="button-container">

                    <button type="submit">
                        Reset Password
                    </button>

                </div>

            </form>

            <p class="paragraph">
                <a href="login.php" class="back-link">
                    &larr; Back to Login
                </a>
            </p>

        </div>

    </main>

    <script src="js/resetPassword.js"></script>

</body>
</html>