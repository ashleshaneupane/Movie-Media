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

$checkQuery = $conn->prepare(
    "SELECT watchedID
     FROM Watched
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
    Add movie to Watched
*/

$insertQuery = $conn->prepare(
    "INSERT INTO Watched (userID, movieID, watchedDate)
     VALUES (?, ?, CURDATE())"
);

$insertQuery->bind_param("ii", $userID, $movieID);

if (!$insertQuery->execute()) {

    echo "error";
    exit;

}


/*
    Remove movie from Watchlist
*/

$deleteQuery = $conn->prepare(
    "DELETE FROM Watchlist
     WHERE userID = ? AND movieID = ?"
);

$deleteQuery->bind_param("ii", $userID, $movieID);
$deleteQuery->execute();


echo "success";

?>