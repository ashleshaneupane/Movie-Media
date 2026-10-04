<?php
include '../includes/adminAuth.php';

$selfID = (int) $_SESSION["userID"];


/*
|--------------------------------------------------------------------------
| Delete user
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    $targetID = (int) ($_POST["userID"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($action === "delete") {

        // Never allow the logged-in admin to be deleted
        if ($targetID === $selfID) {

            $_SESSION["flash"] =
                "You cannot delete your own admin account.";

        } else {

            // Check whether the target is an admin
            $check = $conn->prepare(
                "SELECT role FROM Users WHERE userID = ?"
            );

            $check->bind_param("i", $targetID);
            $check->execute();

            $result = $check->get_result();
            $targetUser = $result->fetch_assoc();

            if (!$targetUser) {

                $_SESSION["flash"] =
                    "User not found.";

            } elseif ($targetUser["role"] === "admin") {

                // Never allow another admin to be deleted
                $_SESSION["flash"] =
                    "Admin accounts cannot be deleted here.";

            } else {

                $stmt = $conn->prepare(
                    "DELETE FROM Users WHERE userID = ?"
                );

                $stmt->bind_param("i", $targetID);
                $stmt->execute();

                $_SESSION["flash"] =
                    "User deleted successfully.";
            }
        }
    }

    header("Location: users.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Search and filter
|--------------------------------------------------------------------------
*/

$search = trim($_GET["q"] ?? "");
$roleFilter = $_GET["role"] ?? "all";

$conditions = [];
$params = [];
$types = "";


/* Search */

if ($search !== "") {

    $conditions[] =
        "(name LIKE ? OR username LIKE ? OR email LIKE ?)";

    $like = "%" . $search . "%";

    $params[] = $like;
    $params[] = $like;
    $params[] = $like;

    $types .= "sss";
}


/* Role filter */

if ($roleFilter === "user") {

    $conditions[] = "role = 'user'";

} elseif ($roleFilter === "admin") {

    $conditions[] = "role = 'admin'";
}


/* Build query */

$sql =
    "SELECT userID, name, username, email, role
     FROM Users";

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY userID DESC";


$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$users = $stmt->get_result();


$pageTitle = "Users";
include 'adminHeader.php';
?>


<div class="page-heading">

    <div>
        <p class="admin-eyebrow">USER MANAGEMENT</p>
        <h1>Users</h1>
        <p class="page-subtitle">
            View and manage Movie Media accounts.
        </p>
    </div>

</div>


<!-- Search and filter -->

<form class="users-toolbar" method="get">

    <input
        type="search"
        name="q"
        value="<?php echo e($search); ?>"
        placeholder="Search name, username or email"
    >

    <select name="role">

        <option value="all"
            <?php echo $roleFilter === "all" ? "selected" : ""; ?>>
            All users
        </option>

        <option value="user"
            <?php echo $roleFilter === "user" ? "selected" : ""; ?>>
            Users only
        </option>

        <option value="admin"
            <?php echo $roleFilter === "admin" ? "selected" : ""; ?>>
            Admin only
        </option>

    </select>

    <button type="submit">
        Search
    </button>

    <?php if ($search !== "" || $roleFilter !== "all"): ?>

        <a href="users.php" class="clear-filter">
            Clear
        </a>

    <?php endif; ?>

</form>


<!-- Users table -->

<div class="admin-table-wrap">

    <table>

        <thead>

            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>

        </thead>


        <tbody>

            <?php if ($users->num_rows === 0): ?>

                <tr>
                    <td colspan="6" class="empty-state">
                        No users found.
                    </td>
                </tr>

            <?php else: ?>

                <?php while ($u = $users->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo (int) $u["userID"]; ?>
                        </td>


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


                        <td>

                            <?php if ($u["role"] === "admin"): ?>

                                <span class="protected-user">
                                    🔒 Protected
                                </span>

                            <?php else: ?>

                                <form
                                    method="post"
                                    class="inline"
                                    onsubmit="return confirm('Delete this user permanently?');"
                                >

                                    <?php echo csrfField(); ?>

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="userID"
                                        value="<?php echo (int) $u["userID"]; ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="danger"
                                    >
                                        Delete
                                    </button>

                                </form>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php endif; ?>

        </tbody>

    </table>

</div>


<?php include 'adminFooter.php'; ?>