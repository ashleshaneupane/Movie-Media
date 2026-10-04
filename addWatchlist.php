<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];

if (!isset($_POST["movieID"]) || !is_numeric($_POST["movieID"])) {
    echo "invalid";
    exit;
}

$movieID = (int) $_POST["movieID"];


/*
    Check if movie is already watched
*/

$watchedQuery = $conn->prepare(
    "SELECT watchedID
     FROM Watched
     WHERE userID = ? AND movieID = ?"
);

$watchedQuery->bind_param("ii", $userID, $movieID);
$watchedQuery->execute();

$watchedResult = $watchedQuery->get_result();

if ($watchedResult->num_rows > 0) {

    echo "watched";
    exit;

}


/*
    Check if movie is already in watchlist
*/

$checkQuery = $conn->prepare(
    "SELECT watchlistID
     FROM Watchlist
     WHERE userID = ? AND movieID = ?"
);

$checkQuery->bind_param("ii", $userID, $movieID);
$checkQuery->execute();

$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows > 0) {

    echo "exists";
    exit;

}


/*
    Add movie to watchlist
*/

$insertQuery = $conn->prepare(
    "INSERT INTO Watchlist (userID, movieID)
     VALUES (?, ?)"
);

$insertQuery->bind_param("ii", $userID, $movieID);

if ($insertQuery->execute()) {

    echo "success";

} else {

    echo "error";

}

?>