<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <main class="forgot-wrapper">

        <div class="forgot-card">

            <h1 id="forgot-heading">Reset Password</h1>

            <p class="subtitle">
                Enter the email that you used to create your account.
            </p>

            <form id="forgotForm">

                <div class="form-group">

                    <label for="email">Email Address</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                    >

                    <span class="error" id="emailError"></span>

                </div>

                <div class="button-container">

                    <button type="submit">
                        Send Reset Link
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

    <script src="js/forgotPassword.js"></script>

</body>

</html>