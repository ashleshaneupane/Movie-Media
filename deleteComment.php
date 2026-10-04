<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/* CHECK COMMENT ID */

if (
    !isset($_POST["commentID"]) ||
    !is_numeric($_POST["commentID"])
) {
    echo "invalid";
    exit;
}


$commentID = (int) $_POST["commentID"];


/* DELETE ONLY USER'S OWN COMMENT */

$deleteQuery = $conn->prepare(
    "DELETE FROM PostComment
     WHERE commentID = ?
     AND userID = ?"
);

$deleteQuery->bind_param(
    "ii",
    $commentID,
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