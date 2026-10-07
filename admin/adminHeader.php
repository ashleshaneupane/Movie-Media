<?php
$current = basename($_SERVER["SCRIPT_NAME"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo e($pageTitle); ?> - Movie Media Admin
    </title>

    <link rel="stylesheet" href="admin.css">
</head>

<body>

<header class="admin-bar">

    <a href="index.php" class="admin-brand">

        <img
            src="../images/logo.png"
            alt="Movie Media"
        >

        <div class="admin-brand-text">
            <strong>Movie Media</strong>
            <span>Admin Panel</span>
        </div>

    </a>

    <nav>

        <a
            href="index.php"
            class="<?php echo $current === 'index.php' ? 'active' : ''; ?>"
        >
            Dashboard
        </a>

        <a
            href="posts.php"
            class="<?php echo in_array($current, ['posts.php', 'postForm.php']) ? 'active' : ''; ?>"
        >
            Posts
        </a>


        <a
    href="reviews.php"
    class="<?php echo $current === 'reviews.php' ? 'active' : ''; ?>"
>
    Reviews
</a>

        <a
            href="movies.php"
            class="<?php echo in_array($current, ['movies.php', 'movieForm.php']) ? 'active' : ''; ?>"
        >
            Movies
        </a>

        <a
            href="users.php"
            class="<?php echo $current === 'users.php' ? 'active' : ''; ?>"
        >
            Users
        </a>

        <a
            href="../home.php"
            class="admin-site-link"
        >
            View Movie Media
        </a>

        <a
            href="../logout.php"
            class="logout-link"
        >
            Logout
        </a>

    </nav>

</header>

<main class="admin-main">

    <?php if (!empty($_SESSION["flash"])): ?>

        <p class="flash">
            <?php echo e($_SESSION["flash"]); ?>
        </p>

        <?php unset($_SESSION["flash"]); ?>

    <?php endif; ?>