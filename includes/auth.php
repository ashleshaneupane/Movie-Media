<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["userID"])) {

    header("Location: login.php?message=login_required");
    exit;

}


/*
|--------------------------------------------------------------------------
| Check whether the logged-in user has been blocked
|--------------------------------------------------------------------------
*/

include_once __DIR__ . "/config.php";

$userID = (int) $_SESSION["userID"];

$blockCheck = $conn->prepare(
    "SELECT isBlocked
     FROM Users
     WHERE userID = ?"
);

$blockCheck->bind_param("i", $userID);
$blockCheck->execute();

$result = $blockCheck->get_result();
$user = $result->fetch_assoc();


if (!$user || (int) $user["isBlocked"] === 1) {

    // Destroy the user's session
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: login.php?message=account_blocked");
    exit;
}

?>