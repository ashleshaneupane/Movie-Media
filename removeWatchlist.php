<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];

if (!isset($_POST["watchlistID"]) || !is_numeric($_POST["watchlistID"])) {
    echo "invalid";
    exit;
}

$watchlistID = (int) $_POST["watchlistID"];

$deleteQuery = $conn->prepare(
    "DELETE FROM Watchlist
     WHERE watchlistID = ? AND userID = ?"
);

$deleteQuery->bind_param("ii", $watchlistID, $userID);

if ($deleteQuery->execute()) {
    echo "success";
} else {
    echo "error";
}

?>