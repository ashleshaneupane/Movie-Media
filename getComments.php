<?php

include 'includes/auth.php';
include 'includes/config.php';

if (
    !isset($_GET["postID"]) ||
    !is_numeric($_GET["postID"])
) {
    echo json_encode([]);
    exit;
}

$postID = (int) $_GET["postID"];

$commentQuery = $conn->prepare(
    "SELECT
        PostComment.commentID,
        PostComment.userID,
        PostComment.commentText,
        Users.username,
        Users.name
     FROM PostComment
     INNER JOIN Users
        ON PostComment.userID = Users.userID
     WHERE PostComment.postID = ?
     ORDER BY PostComment.commentID ASC"
);

$commentQuery->bind_param(
    "i",
    $postID
);

$commentQuery->execute();

$result = $commentQuery->get_result();

$comments = [];

while ($comment = $result->fetch_assoc()) {
    $comments[] = $comment;
}

header("Content-Type: application/json");

echo json_encode($comments);

?>