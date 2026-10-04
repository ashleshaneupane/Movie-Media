<?php

include 'includes/auth.php';
include 'includes/config.php';


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo "invalid";

    exit;
}


$currentUserID =
    (int) $_SESSION["userID"];


$requestID =
    (int) ($_POST["requestID"] ?? 0);


$action =
    $_POST["action"] ?? "";


if (
    $requestID <= 0 ||
    !in_array(
        $action,
        ["accept", "decline"],
        true
    )
) {

    echo "invalid";

    exit;
}


/* GET REQUEST */

$requestQuery = $conn->prepare(
    "SELECT
        requestID,
        senderID,
        receiverID,
        status
     FROM FriendRequest
     WHERE requestID = ?
     AND receiverID = ?
     AND status = 'pending'
     LIMIT 1"
);


$requestQuery->bind_param(
    "ii",
    $requestID,
    $currentUserID
);


$requestQuery->execute();


$requestResult =
    $requestQuery->get_result();


if ($requestResult->num_rows === 0) {

    echo "not_found";

    exit;
}


$request =
    $requestResult->fetch_assoc();


$senderID =
    (int) $request["senderID"];


/* =========================
   ACCEPT
========================= */

if ($action === "accept") {


    $update = $conn->prepare(
        "UPDATE FriendRequest
         SET status = 'accepted'
         WHERE requestID = ?
         AND receiverID = ?"
    );


    $update->bind_param(
        "ii",
        $requestID,
        $currentUserID
    );


    if ($update->execute()) {


        /*
            Notify original sender
        */

        $notificationType =
            "friend_accepted";


        $notificationMessage =
            "accepted your friend request";


        $notification = $conn->prepare(
            "INSERT INTO Notification
            (
                userID,
                senderID,
                requestID,
                type,
                message
            )
            VALUES (?, ?, ?, ?, ?)"
        );


        $notification->bind_param(
            "iiiss",
            $senderID,
            $currentUserID,
            $requestID,
            $notificationType,
            $notificationMessage
        );


        $notification->execute();


        /*
            Mark original notification as read
        */

        $readNotification = $conn->prepare(
            "UPDATE Notification
             SET isRead = 1
             WHERE userID = ?
             AND senderID = ?
             AND requestID = ?
             AND type = 'friend_request'"
        );


        $readNotification->bind_param(
            "iii",
            $currentUserID,
            $senderID,
            $requestID
        );


        $readNotification->execute();


        echo "accepted";


    } else {

        echo "error";

    }


    exit;
}


/* =========================
   DECLINE
========================= */

if ($action === "decline") {


    $update = $conn->prepare(
        "UPDATE FriendRequest
         SET status = 'rejected'
         WHERE requestID = ?
         AND receiverID = ?"
    );


    $update->bind_param(
        "ii",
        $requestID,
        $currentUserID
    );


    if ($update->execute()) {


        /*
            Mark notification as read
        */

        $readNotification = $conn->prepare(
            "UPDATE Notification
             SET isRead = 1
             WHERE userID = ?
             AND senderID = ?
             AND requestID = ?
             AND type = 'friend_request'"
        );


        $readNotification->bind_param(
            "iii",
            $currentUserID,
            $senderID,
            $requestID
        );


        $readNotification->execute();


        echo "declined";


    } else {

        echo "error";

    }


    exit;
}

?>