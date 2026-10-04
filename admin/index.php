<?php
include '../includes/adminAuth.php';

$userCount = $conn->query(
    "SELECT COUNT(*) AS c FROM Users"
)->fetch_assoc()["c"];

$adminCount = $conn->query(
    "SELECT COUNT(*) AS c FROM Users WHERE role = 'admin'"
)->fetch_assoc()["c"];

$movieCount = $conn->query(
    "SELECT COUNT(*) AS c FROM movie"
)->fetch_assoc()["c"];

$recentUsers = $conn->query(
    "SELECT username, name, email, role
     FROM Users
     ORDER BY userID DESC
     LIMIT 5"
);

$pageTitle = "Dashboard";
include 'adminHeader.php';
?>

<section class="dashboard-heading">
    <div>
        <p class="admin-eyebrow">MOVIE MEDIA</p>
        <h1>Admin Dashboard</h1>
        <p class="dashboard-subtitle">
            Manage your movie community from one place.
        </p>
    </div>
</section>


<!-- Statistics -->

<div class="stats">

    <div class="stat">
        <span class="stat-label">Total Users</span>
        <strong><?php echo (int) $userCount; ?></strong>
        <small>Registered accounts</small>
    </div>

    <div class="stat">
        <span class="stat-label">Admins</span>
        <strong><?php echo (int) $adminCount; ?></strong>
        <small>System administrators</small>
    </div>

    <div class="stat">
        <span class="stat-label">Movies</span>
        <strong><?php echo (int) $movieCount; ?></strong>
        <small>Movies in database</small>
    </div>

</div>


<!-- Quick actions -->

<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <p class="admin-eyebrow">QUICK ACTIONS</p>
            <h2>Manage Movie Media</h2>
        </div>
    </div>

    <div class="quick-actions">

        <a href="movies.php" class="quick-card">
            <span class="quick-icon">🎬</span>
            <div>
                <strong>Manage Movies</strong>
                <p>Add, edit, search and remove movies.</p>
            </div>
        </a>

        <a href="users.php" class="quick-card">
            <span class="quick-icon">👥</span>
            <div>
                <strong>Manage Users</strong>
                <p>View and manage registered users.</p>
            </div>
        </a>

        <a href="../home.php" class="quick-card">
            <span class="quick-icon">🏠</span>
            <div>
                <strong>View Movie Media</strong>
                <p>Return to the main Movie Media website.</p>
            </div>
        </a>

    </div>

</section>


<!-- Recent users -->

<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <p class="admin-eyebrow">RECENT ACTIVITY</p>
            <h2>Newest Users</h2>
        </div>

        <a href="users.php" class="section-link">
            View all users →
        </a>
    </div>


    <div class="admin-table-wrap">

        <table>

            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
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