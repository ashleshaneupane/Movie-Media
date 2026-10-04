<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/* CHECK POST ID */

if (
    !isset($_POST["postID"]) ||
    !is_numeric($_POST["postID"])
) {
    echo "invalid";
    exit;
}

$postID = (int) $_POST["postID"];


/* DELETE POST */

$deleteQuery = $conn->prepare(
    "DELETE FROM Post
     WHERE postID = ?
     AND userID = ?"
);

$deleteQuery->bind_param(
    "ii",
    $postID,
    $userID
);


if ($deleteQuery->execute()) {

    if ($deleteQuery->affected_rows > 0) {

        echo "success";

    } else {

        echo "not_allowed";

    }

} else {

    echo "error";

}

?>