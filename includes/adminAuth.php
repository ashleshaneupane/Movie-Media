<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . "/config.php";


/*
|--------------------------------------------------------------------------
| Check login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["userID"])) {
    header("Location: ../login.php?message=login_required");
    exit;
}


/*
|--------------------------------------------------------------------------
| Admin-only access
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: ../home.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Only the official Movie Media admin
|--------------------------------------------------------------------------
*/

$adminUsername = "MovieMEDIA_admin";

if (
    !isset($_SESSION["username"]) ||
    $_SESSION["username"] !== $adminUsername
) {
    header("Location: ../home.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Escape output
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}


/*
|--------------------------------------------------------------------------
| CSRF protection
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] =
        bin2hex(random_bytes(32));
}


function csrfField()
{
    return
        '<input type="hidden" name="csrf_token" value="' .
        e($_SESSION["csrf_token"]) .
        '">';
}


function requireValidPost()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        exit("Method Not Allowed");
    }

    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals(
            $_SESSION["csrf_token"] ?? "",
            $_POST["csrf_token"]
        )
    ) {
        http_response_code(403);
        exit("Invalid request.");
    }
}
?>