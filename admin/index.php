<?php
include '../includes/adminAuth.php';

$userCount = $conn->query("SELECT COUNT(*) AS c FROM Users")->fetch_assoc()["c"];
$adminCount = $conn->query("SELECT COUNT(*) AS c FROM Users WHERE role = 'admin'")->fetch_assoc()["c"];
$movieCount = $conn->query("SELECT COUNT(*) AS c FROM movie")->fetch_assoc()["c"];

$recentUsers = $conn->query(
    "SELECT username, name, email, role
     FROM Users
     ORDER BY userID DESC
     LIMIT 5"
);

$pageTitle = "Dashboard";
include 'adminHeader.php';
?>

<h1>Dashboard</h1>

<div class="stats">
    <div class="stat"><span>Users</span><strong><?php echo (int) $userCount; ?></strong></div>
    <div class="stat"><span>Admins</span><strong><?php echo (int) $adminCount; ?></strong></div>
    <div class="stat"><span>Movies</span><strong><?php echo (int) $movieCount; ?></strong></div>
</div>

<h2>Newest users</h2>

<table>
    <tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th></tr>
    <?php while ($u = $recentUsers->fetch_assoc()): ?>
        <tr>
            <td><?php echo e($u["name"]); ?></td>
            <td>@<?php echo e($u["username"]); ?></td>
            <td><?php echo e($u["email"]); ?></td>
            <td><?php echo e($u["role"]); ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'adminFooter.php'; ?>
