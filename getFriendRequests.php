<?php

include 'includes/auth.php';
include 'includes/config.php';

$currentUserID =
    (int) $_SESSION["userID"];


/* =========================
   GET INCOMING REQUESTS
========================= */

$requestQuery = $conn->prepare(
    "SELECT
        FriendRequest.requestID,
        FriendRequest.senderID,

        Users.name,
        Users.username,
        Users.profilePicture

     FROM FriendRequest

     INNER JOIN Users
        ON FriendRequest.senderID =
           Users.userID

     WHERE
        FriendRequest.receiverID = ?
        AND FriendRequest.status = 'pending'
        AND Users.role != 'admin'

     ORDER BY FriendRequest.requestID DESC"
);


$requestQuery->bind_param(
    "i",
    $currentUserID
);


$requestQuery->execute();


$result =
    $requestQuery->get_result();


$requests = [];


while (
    $request =
        $result->fetch_assoc()
) {

    $requests[] = $request;

}


header(
    "Content-Type: application/json"
);


echo json_encode(
    $requests
);

?>