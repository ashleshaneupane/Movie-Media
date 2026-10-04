<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . "/config.php";


/* =========================
   CHECK LOGIN
========================= */

if (!isset($_SESSION["userID"])) {
    header("Location: ../login.php?message=login_required");
    exit;
}


/* =========================
   CHECK ADMIN ROLE
========================= */

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: ../home.php");
    exit;
}


/* =========================
   ESCAPE OUTPUT
========================= */

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}


/* =========================
   CSRF TOKEN
========================= */

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] =
        bin2hex(random_bytes(32));
}


/* =========================
   CSRF FIELD
========================= */

function csrfField()
{
    return
        '<input type="hidden" name="csrf_token" value="' .
        e($_SESSION["csrf_token"]) .
        '">';
}


/* =========================
   VALIDATE POST
========================= */

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