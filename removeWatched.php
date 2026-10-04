<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];

if (!isset($_POST["watchedID"]) || !is_numeric($_POST["watchedID"])) {
    echo "invalid";
    exit;
}

$watchedID = (int) $_POST["watchedID"];

$deleteQuery = $conn->prepare(
    "DELETE FROM Watched
     WHERE watchedID = ? AND userID = ?"
);

$deleteQuery->bind_param("ii", $watchedID, $userID);

if ($deleteQuery->execute()) {
    echo "success";
} else {
    echo "error";
}

?>