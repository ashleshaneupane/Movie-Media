<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/* =========================
   CHECK REVIEW ID
========================= */

if (
    !isset($_POST["reviewID"]) ||
    !is_numeric($_POST["reviewID"])
) {
    echo "invalid";
    exit;
}


$reviewID = (int) $_POST["reviewID"];


/* =========================
   DELETE REVIEW
========================= */

$deleteQuery = $conn->prepare(
    "DELETE FROM Review
     WHERE reviewID = ? AND userID = ?"
);

$deleteQuery->bind_param(
    "ii",
    $reviewID,
    $userID
);


if ($deleteQuery->execute()) {

    echo "success";

} else {

    echo "error";

}

?>