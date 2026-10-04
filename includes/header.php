<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdmin =
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "admin";

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

<header>

    <nav>

        <img
            class="logo"
            src="images/logo.png"
            alt="logo"
        >

        <div class="nav-links">

            <a href="home.php">
                Home
            </a>

            <a href="search.php">
                Search
            </a>


            <?php if ($isAdmin): ?>

                <a href="admin/index.php">
                    Admin Panel
                </a>

                <a href="logout.php">
                    Logout
                </a>


            <?php else: ?>

                <a href="connect.php">
                    Connect
                </a>

                <a href="notification.php">
                    Notifications
                </a>

                <a href="account.php">
                    Account
                </a>

            <?php endif; ?>

        </div>

    </nav>

</header>