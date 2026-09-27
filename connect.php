<?php
include 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connect - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="connect-container">

        <div class="connect-card">

            <p class="connect-label">
                WELCOME TO MOVIE MEDIA
            </p>


            <div class="connect-icon">
                <img id="icon-user" src="images/Users.png" alt="users">
            </div>


            <h1>
                Find Friends
            </h1>


            <p class="connect-description">
                Connect with other movie lovers,
                discover new people and share
                your favorite movies together.
            </p>


            <a
                href="findFriends.php"
                class="find-friends-btn"
            >
                Find Friends
            </a>


            <a
                href="home.php"
                class="skip-link"
            >
                Skip for now
            </a>

        </div>

    </main>


</body>

</html>