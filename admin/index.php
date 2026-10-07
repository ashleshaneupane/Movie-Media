
<?php

include '../includes/adminAuth.php';


/* =========================================================
   DASHBOARD STATISTICS
========================================================= */

$userCount = $conn->query(
    "SELECT COUNT(*) AS c
     FROM Users"
)->fetch_assoc()["c"];


$movieCount = $conn->query(
    "SELECT COUNT(*) AS c
     FROM movie"
)->fetch_assoc()["c"];


$postCount = $conn->query(
    "SELECT COUNT(*) AS c
     FROM post"
)->fetch_assoc()["c"];


$reviewCount = $conn->query(
    "SELECT COUNT(*) AS c
     FROM Review"
)->fetch_assoc()["c"];


$spoilerCount = $conn->query(
    "SELECT COUNT(*) AS c
     FROM Review
     WHERE spoiler = 1"
)->fetch_assoc()["c"];


$blockedCount = $conn->query(
    "SELECT COUNT(*) AS c
     FROM Users
     WHERE isBlocked = 1"
)->fetch_assoc()["c"];


/* =========================================================
   RECENT USERS
========================================================= */

$recentUsers = $conn->query(
    "SELECT
        username,
        name,
        email,
        role
     FROM Users
     ORDER BY userID DESC
     LIMIT 5"
);


$pageTitle = "Dashboard";

include 'adminHeader.php';

?>


<!-- =====================================================
     DASHBOARD HEADING
====================================================== -->

<section class="dashboard-heading">

    <div>

        <p class="admin-eyebrow">
            MOVIE MEDIA
        </p>

        <h1>
            Admin Dashboard
        </h1>

        <p class="dashboard-subtitle">
            Manage and monitor your movie community from one place.
        </p>

    </div>

</section>


<!-- =====================================================
     PLATFORM STATISTICS
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">

        <div>

            <p class="admin-eyebrow">
                PLATFORM OVERVIEW
            </p>

            <h2>
                Movie Media at a glance
            </h2>

        </div>

    </div>


    <div class="stats">


        <!-- USERS -->

        <div class="stat">

            <span class="stat-icon">
                👥
            </span>

            <span class="stat-label">
                Total Users
            </span>

            <strong>
                <?php echo (int) $userCount; ?>
            </strong>

            <small>
                Registered accounts
            </small>

        </div>


        <!-- MOVIES -->

        <div class="stat">

            <span class="stat-icon">
                🎬
            </span>

            <span class="stat-label">
                Movies
            </span>

            <strong>
                <?php echo (int) $movieCount; ?>
            </strong>

            <small>
                Movies in catalogue
            </small>

        </div>


        <!-- POSTS -->

        <div class="stat">

            <span class="stat-icon">
                📝
            </span>

            <span class="stat-label">
                Posts
            </span>

            <strong>
                <?php echo (int) $postCount; ?>
            </strong>

            <small>
                Community posts
            </small>

        </div>


        <!-- REVIEWS -->

        <div class="stat">

            <span class="stat-icon">
                ⭐
            </span>

            <span class="stat-label">
                Reviews
            </span>

            <strong>
                <?php echo (int) $reviewCount; ?>
            </strong>

            <small>
                Movie reviews
            </small>

        </div>


        <!-- SPOILERS -->

        <div class="stat">

            <span class="stat-icon">
                ⚠️
            </span>

            <span class="stat-label">
                Spoilers
            </span>

            <strong>
                <?php echo (int) $spoilerCount; ?>
            </strong>

            <small>
                Reviews marked spoiler
            </small>

        </div>


        <!-- BLOCKED -->

        <div class="stat">

            <span class="stat-icon">
                🚫
            </span>

            <span class="stat-label">
                Blocked
            </span>

            <strong>
                <?php echo (int) $blockedCount; ?>
            </strong>

            <small>
                Restricted accounts
            </small>

        </div>


    </div>

</section>


<!-- =====================================================
     QUICK ACTIONS
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">

        <div>

            <p class="admin-eyebrow">
                QUICK ACTIONS
            </p>

            <h2>
                Manage Movie Media
            </h2>

        </div>

    </div>


    <div class="quick-actions">


        <!-- MOVIES -->

        <a
            href="movies.php"
            class="quick-card"
        >

            <span class="quick-icon">
                🎬
            </span>

            <div>

                <strong>
                    Manage Movies
                </strong>

                <p>
                    Add, edit, search and remove movies.
                </p>

            </div>

        </a>


        <!-- USERS -->

        <a
            href="users.php"
            class="quick-card"
        >

            <span class="quick-icon">
                👥
            </span>

            <div>

                <strong>
                    Manage Users
                </strong>

                <p>
                    View accounts and manage access.
                </p>

            </div>

        </a>


        <!-- POSTS -->

        <a
            href="posts.php"
            class="quick-card"
        >

            <span class="quick-icon">
                📝
            </span>

            <div>

                <strong>
                    Moderate Posts
                </strong>

                <p>
                    Review community posts and remove content.
                </p>

            </div>

        </a>


        <!-- REVIEWS -->

        <a
            href="reviews.php"
            class="quick-card"
        >

            <span class="quick-icon">
                ⭐
            </span>

            <div>

                <strong>
                    Moderate Reviews
                </strong>

                <p>
                    Review feedback and manage spoiler flags.
                </p>

            </div>

        </a>


    </div>

</section>


<!-- =====================================================
     RECENT USERS
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">

        <div>

            <p class="admin-eyebrow">
                RECENT ACTIVITY
            </p>

            <h2>
                Newest Users
            </h2>

        </div>


        <a
            href="users.php"
            class="section-link"
        >
            View all users →
        </a>

    </div>


    <div class="admin-table-wrap">

        <table>

            <thead>

                <tr>

                    <th>
                        Name
                    </th>

                    <th>
                        Username
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Role
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php while ($u = $recentUsers->fetch_assoc()): ?>

                    <tr>

                        <td>

                            <strong>
                                <?php echo e($u["name"]); ?>
                            </strong>

                        </td>


                        <td>
                            @<?php echo e($u["username"]); ?>
                        </td>


                        <td>
                            <?php echo e($u["email"]); ?>
                        </td>


                        <td>

                            <?php if ($u["role"] === "admin"): ?>

                                <span class="role-badge admin">
                                    Admin
                                </span>

                            <?php else: ?>

                                <span class="role-badge">
                                    User
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</section>


<?php include 'adminFooter.php'; ?>
