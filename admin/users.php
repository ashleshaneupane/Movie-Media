
<?php
include '../includes/adminAuth.php';

$selfID = (int) $_SESSION["userID"];


/*
|--------------------------------------------------------------------------
| User actions
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    $targetID = (int) ($_POST["userID"] ?? 0);
    $action = $_POST["action"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Delete user
    |--------------------------------------------------------------------------
    */

    if ($action === "delete") {

        if ($targetID === $selfID) {

            $_SESSION["flash"] =
                "You cannot delete your own admin account.";

        } else {

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

                $_SESSION["flash"] =
                    "Admin accounts cannot be deleted here.";

            } else {

                $stmt = $conn->prepare(
                    "DELETE FROM Users WHERE userID = ?"
                );

                $stmt->bind_param("i", $targetID);

                if ($stmt->execute()) {

                    $_SESSION["flash"] =
                        "User deleted successfully.";

                } else {

                    $_SESSION["flash"] =
                        "Could not delete user.";
                }
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Block user
    |--------------------------------------------------------------------------
    */

    elseif ($action === "block") {

        if ($targetID === $selfID) {

            $_SESSION["flash"] =
                "You cannot block your own admin account.";

        } else {

            $check = $conn->prepare(
                "SELECT role, isBlocked
                 FROM Users
                 WHERE userID = ?"
            );

            $check->bind_param("i", $targetID);
            $check->execute();

            $result = $check->get_result();
            $targetUser = $result->fetch_assoc();

            if (!$targetUser) {

                $_SESSION["flash"] =
                    "User not found.";

            } elseif ($targetUser["role"] === "admin") {

                $_SESSION["flash"] =
                    "Admin accounts cannot be blocked here.";

            } else {

                $stmt = $conn->prepare(
                    "UPDATE Users
                     SET isBlocked = 1
                     WHERE userID = ?"
                );

                $stmt->bind_param("i", $targetID);

                if ($stmt->execute()) {

                    $_SESSION["flash"] =
                        "User blocked successfully.";

                } else {

                    $_SESSION["flash"] =
                        "Could not block user.";
                }
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Unblock user
    |--------------------------------------------------------------------------
    */

    elseif ($action === "unblock") {

        if ($targetID === $selfID) {

            $_SESSION["flash"] =
                "You cannot modify your own admin account.";

        } else {

            $check = $conn->prepare(
                "SELECT role
                 FROM Users
                 WHERE userID = ?"
            );

            $check->bind_param("i", $targetID);
            $check->execute();

            $result = $check->get_result();
            $targetUser = $result->fetch_assoc();

            if (!$targetUser) {

                $_SESSION["flash"] =
                    "User not found.";

            } elseif ($targetUser["role"] === "admin") {

                $_SESSION["flash"] =
                    "Admin accounts cannot be modified here.";

            } else {

                $stmt = $conn->prepare(
                    "UPDATE Users
                     SET isBlocked = 0
                     WHERE userID = ?"
                );

                $stmt->bind_param("i", $targetID);

                if ($stmt->execute()) {

                    $_SESSION["flash"] =
                        "User unblocked successfully.";

                } else {

                    $_SESSION["flash"] =
                        "Could not unblock user.";
                }
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


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$usersPerPage = 8;

$page = (int) ($_GET["page"] ?? 1);

if ($page < 1) {
    $page = 1;
}


/*
|--------------------------------------------------------------------------
| Count matching users
|--------------------------------------------------------------------------
*/

$countSql =
    "SELECT COUNT(*) AS total
     FROM Users";

if (!empty($conditions)) {

    $countSql .=
        " WHERE " .
        implode(" AND ", $conditions);
}


$countStmt = $conn->prepare($countSql);

if (!empty($params)) {

    $countStmt->bind_param(
        $types,
        ...$params
    );
}

$countStmt->execute();

$countResult =
    $countStmt->get_result();

$totalUsers =
    (int) $countResult
        ->fetch_assoc()["total"];


$totalPages = max(
    1,
    (int) ceil(
        $totalUsers / $usersPerPage
    )
);


if ($page > $totalPages) {
    $page = $totalPages;
}


$offset =
    ($page - 1) * $usersPerPage;


/*
|--------------------------------------------------------------------------
| Get users for current page
|--------------------------------------------------------------------------
*/

$sql =
    "SELECT
        userID,
        name,
        username,
        email,
        role,
        isBlocked
     FROM Users";


if (!empty($conditions)) {

    $sql .=
        " WHERE " .
        implode(" AND ", $conditions);
}


$sql .=
    " ORDER BY userID DESC
      LIMIT ? OFFSET ?";


$userParams = $params;

$userTypes =
    $types . "ii";

$userParams[] =
    $usersPerPage;

$userParams[] =
    $offset;


$stmt =
    $conn->prepare($sql);


$stmt->bind_param(
    $userTypes,
    ...$userParams
);


$stmt->execute();

$users =
    $stmt->get_result();


$pageTitle = "Users";

include 'adminHeader.php';

?>


<div class="page-heading">

    <div>

        <p class="admin-eyebrow">
            USER MANAGEMENT
        </p>

        <h1>
            Users
        </h1>

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

        <option
            value="all"
            <?php echo
                $roleFilter === "all"
                ? "selected"
                : "";
            ?>
        >
            All users
        </option>


        <option
            value="user"
            <?php echo
                $roleFilter === "user"
                ? "selected"
                : "";
            ?>
        >
            Users only
        </option>


        <option
            value="admin"
            <?php echo
                $roleFilter === "admin"
                ? "selected"
                : "";
            ?>
        >
            Admin only
        </option>

    </select>


    <button type="submit">
        Search
    </button>


    <?php if (
        $search !== "" ||
        $roleFilter !== "all"
    ): ?>

        <a
            href="users.php"
            class="clear-filter"
        >
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
                <th>Status</th>
                <th>Action</th>

            </tr>

        </thead>


        <tbody>

            <?php if ($users->num_rows === 0): ?>

                <tr>

                    <td
                        colspan="7"
                        class="empty-state"
                    >
                        No users found.
                    </td>

                </tr>

            <?php else: ?>

                <?php while (
                    $u = $users->fetch_assoc()
                ): ?>

                    <tr>

                        <td>
                            <?php echo
                                (int) $u["userID"];
                            ?>
                        </td>


                        <td>

                            <strong>
                                <?php echo
                                    e($u["name"]);
                                ?>
                            </strong>

                        </td>


                        <td>
                            @<?php echo
                                e($u["username"]);
                            ?>
                        </td>


                        <td>
                            <?php echo
                                e($u["email"]);
                            ?>
                        </td>


                        <td>

                            <?php if (
                                $u["role"] === "admin"
                            ): ?>

                                <span
                                    class="role-badge admin"
                                >
                                    Admin
                                </span>

                            <?php else: ?>

                                <span
                                    class="role-badge"
                                >
                                    User
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php if (
                                (int) $u["isBlocked"] === 1
                            ): ?>

                                <span
                                    class="role-badge"
                                >
                                    Blocked
                                </span>

                            <?php else: ?>

                                <span
                                    class="role-badge"
                                >
                                    Active
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php if (
                                $u["role"] === "admin"
                            ): ?>

                                <span
                                    class="protected-user"
                                >
                                    🔒 Protected
                                </span>

                            <?php else: ?>


                                <?php if (
                                    (int) $u["isBlocked"] === 1
                                ): ?>

                                    <!-- Unblock -->

                                    <form
                                        method="post"
                                        class="inline"
                                    >

                                        <?php echo
                                            csrfField();
                                        ?>


                                        <input
                                            type="hidden"
                                            name="action"
                                            value="unblock"
                                        >


                                        <input
                                            type="hidden"
                                            name="userID"
                                            value="<?php echo
                                                (int) $u["userID"];
                                            ?>"
                                        >


                                        <button
                                            type="submit"
                                        >
                                            Unblock
                                        </button>

                                    </form>


                                <?php else: ?>

                                    <!-- Block -->

                                    <form
                                        method="post"
                                        class="inline"
                                        onsubmit="return confirm('Block this user?');"
                                    >

                                        <?php echo
                                            csrfField();
                                        ?>


                                        <input
                                            type="hidden"
                                            name="action"
                                            value="block"
                                        >


                                        <input
                                            type="hidden"
                                            name="userID"
                                            value="<?php echo
                                                (int) $u["userID"];
                                            ?>"
                                        >


                                        <button
                                            type="submit"
                                        >
                                            Block
                                        </button>

                                    </form>

                                <?php endif; ?>


                                <!-- Delete -->

                                <form
                                    method="post"
                                    class="inline"
                                    onsubmit="return confirm('Delete this user permanently?');"
                                >

                                    <?php echo
                                        csrfField();
                                    ?>


                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >


                                    <input
                                        type="hidden"
                                        name="userID"
                                        value="<?php echo
                                            (int) $u["userID"];
                                        ?>"
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


<!-- Pagination -->

<?php if ($totalPages > 1): ?>

    <div class="pagination">

        <?php if ($page > 1): ?>

            <a
                href="?<?php
                    echo http_build_query([
                        "q" => $search,
                        "role" => $roleFilter,
                        "page" => $page - 1
                    ]);
                ?>"
            >
                ← Previous
            </a>

        <?php endif; ?>


        <?php for (
            $i = 1;
            $i <= $totalPages;
            $i++
        ): ?>

            <a
                href="?<?php
                    echo http_build_query([
                        "q" => $search,
                        "role" => $roleFilter,
                        "page" => $i
                    ]);
                ?>"
                class="<?php echo
                    $i === $page
                    ? "active"
                    : "";
                ?>"
            >
                <?php echo $i; ?>
            </a>

        <?php endfor; ?>


        <?php if ($page < $totalPages): ?>

            <a
                href="?<?php
                    echo http_build_query([
                        "q" => $search,
                        "role" => $roleFilter,
                        "page" => $page + 1
                    ]);
                ?>"
            >
                Next →
            </a>

        <?php endif; ?>

    </div>

<?php endif; ?>


<?php include 'adminFooter.php'; ?>
