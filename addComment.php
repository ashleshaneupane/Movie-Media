<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];

if (
    !isset($_POST["postID"]) ||
    !is_numeric($_POST["postID"]) ||
    !isset($_POST["commentText"])
) {
    echo "invalid";
    exit;
}

$postID = (int) $_POST["postID"];
$commentText = trim($_POST["commentText"]);


/* CHECK COMMENT */

if ($commentText === "") {
    echo "empty";
    exit;
}


/* CHECK POST EXISTS */

$postCheck = $conn->prepare(
    "SELECT postID
     FROM Post
     WHERE postID = ?"
);

$postCheck->bind_param(
    "i",
    $postID
);

$postCheck->execute();

$postResult = $postCheck->get_result();

if ($postResult->num_rows === 0) {
    echo "invalid";
    exit;
}


/* ADD COMMENT */

$commentQuery = $conn->prepare(
    "INSERT INTO PostComment
     (postID, userID, commentText)
     VALUES (?, ?, ?)"
);

$commentQuery->bind_param(
    "iis",
    $postID,
    $userID,
    $commentText
);

if ($commentQuery->execute()) {
    echo "success";
} else {
    echo "error";
}

?>