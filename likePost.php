<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];

if (
    !isset($_POST["postID"]) ||
    !is_numeric($_POST["postID"])
) {
    echo "invalid";
    exit;
}

$postID = (int) $_POST["postID"];


/* CHECK IF ALREADY LIKED */

$checkQuery = $conn->prepare(
    "SELECT likeID
     FROM PostLike
     WHERE postID = ? AND userID = ?"
);

$checkQuery->bind_param(
    "ii",
    $postID,
    $userID
);

$checkQuery->execute();

$result = $checkQuery->get_result();


/* REMOVE LIKE */

if ($result->num_rows > 0) {

    $deleteQuery = $conn->prepare(
        "DELETE FROM PostLike
         WHERE postID = ? AND userID = ?"
    );

    $deleteQuery->bind_param(
        "ii",
        $postID,
        $userID
    );

    if ($deleteQuery->execute()) {
        echo "unliked";
    } else {
        echo "error";
    }

    exit;
}


/* ADD LIKE */

$insertQuery = $conn->prepare(
    "INSERT INTO PostLike
     (postID, userID)
     VALUES (?, ?)"
);

$insertQuery->bind_param(
    "ii",
    $postID,
    $userID
);

if ($insertQuery->execute()) {
    echo "liked";
} else {
    echo "error";
}

?>