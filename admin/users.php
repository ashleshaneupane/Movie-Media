<?php
include '../includes/adminAuth.php';

$selfID = (int) $_SESSION["userID"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    $targetID = (int) ($_POST["userID"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($targetID === $selfID) {
        $_SESSION["flash"] = "You cannot change or delete your own account here.";
    } elseif ($action === "role") {
        $newRole = ($_POST["role"] ?? "") === "admin" ? "admin" : "user";
        $stmt = $conn->prepare("UPDATE Users SET role = ? WHERE userID = ?");
        $stmt->bind_param("si", $newRole, $targetID);
        $stmt->execute();
        $_SESSION["flash"] = "Role updated.";
    } elseif ($action === "delete") {
        $stmt = $conn->prepare("DELETE FROM Users WHERE userID = ?");
        $stmt->bind_param("i", $targetID);
        $stmt->execute();
        $_SESSION["flash"] = "User deleted.";
    }

    header("Location: users.php");
    exit;
}

$search = trim($_GET["q"] ?? "");
$like = "%" . $search . "%";

$stmt = $conn->prepare(
    "SELECT userID, name, username, email, role
     FROM Users
     WHERE name LIKE ? OR username LIKE ? OR email LIKE ?
     ORDER BY userID DESC"
);
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$users = $stmt->get_result();

$pageTitle = "Users";
include 'adminHeader.php';
?>

<h1>Users</h1>

<form class="toolbar" method="get">
    <input type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search name, username or email">
    <button type="submit">Search</button>
</form>

<table>
    <tr><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr>

    <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo (int) $u["userID"]; ?></td>
            <td><?php echo e($u["name"]); ?></td>
            <td>@<?php echo e($u["username"]); ?></td>
            <td><?php echo e($u["email"]); ?></td>
            <td>
                <?php if ((int) $u["userID"] === $selfID): ?>
                    <?php echo e($u["role"]); ?> (you)
                <?php else: ?>
                    <form method="post" class="inline">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="role">
                        <input type="hidden" name="userID" value="<?php echo (int) $u["userID"]; ?>">
                        <select name="role" onchange="this.form.submit()">
                            <option value="user" <?php echo $u["role"] !== "admin" ? "selected" : ""; ?>>user</option>
                            <option value="admin" <?php echo $u["role"] === "admin" ? "selected" : ""; ?>>admin</option>
                        </select>
                    </form>
                <?php endif; ?>
            </td>
            <td>
                <?php if ((int) $u["userID"] !== $selfID): ?>
                    <form method="post" class="inline" onsubmit="return confirm('Delete this user permanently?');">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="userID" value="<?php echo (int) $u["userID"]; ?>">
                        <button type="submit" class="danger">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'adminFooter.php'; ?>
