<?php

include 'includes/auth.php';
include 'includes/config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "invalid";
    exit;
}

$currentUserID = (int) $_SESSION["userID"];
$receiverID = (int) ($_POST["receiverID"] ?? 0);

if ($receiverID <= 0 || $receiverID === $currentUserID) {
    echo "invalid";
    exit;
}


/* CHECK RECEIVER */

$userCheck = $conn->prepare(
    "SELECT userID
     FROM Users
     WHERE userID = ?
     AND role != 'admin'"
);

$userCheck->bind_param("i", $receiverID);
$userCheck->execute();

$userResult = $userCheck->get_result();

if ($userResult->num_rows === 0) {
    echo "invalid";
    exit;
}


/* CHECK EXISTING REQUEST */

$requestCheck = $conn->prepare(
    "SELECT requestID, senderID, receiverID, status
     FROM FriendRequest
     WHERE
        (senderID = ? AND receiverID = ?)
        OR
        (senderID = ? AND receiverID = ?)
     LIMIT 1"
);

$requestCheck->bind_param(
    "iiii",
    $currentUserID,
    $receiverID,
    $receiverID,
    $currentUserID
);

$requestCheck->execute();

$requestResult = $requestCheck->get_result();

if ($requestResult->num_rows > 0) {

    $request = $requestResult->fetch_assoc();

    if ($request["status"] === "accepted") {
        echo "friends";
        exit;
    }

    if ($request["status"] === "pending") {

        if ((int) $request["senderID"] === $currentUserID) {
            echo "sent";
        } else {
            echo "received";
        }

        exit;
    }
}


/* CREATE FRIEND REQUEST */

$insert = $conn->prepare(
    "INSERT INTO FriendRequest
    (senderID, receiverID, status)
    VALUES (?, ?, 'pending')"
);

$insert->bind_param(
    "ii",
    $currentUserID,
    $receiverID
);

if (!$insert->execute()) {
    echo "error";
    exit;
}


/* GET THE REQUEST ID DIRECTLY FROM DATABASE */

$newRequestQuery = $conn->prepare(
    "SELECT requestID
     FROM FriendRequest
     WHERE senderID = ?
     AND receiverID = ?
     AND status = 'pending'
     ORDER BY requestID DESC
     LIMIT 1"
);

$newRequestQuery->bind_param(
    "ii",
    $currentUserID,
    $receiverID
);

$newRequestQuery->execute();

$newRequestResult = $newRequestQuery->get_result();

if ($newRequestResult->num_rows === 0) {
    echo "request_id_error";
    exit;
}

$newRequest = $newRequestResult->fetch_assoc();

$requestID = (int) $newRequest["requestID"];


/* CREATE NOTIFICATION */

$notificationType = "friend_request";
$notificationMessage = "sent you a friend request";

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
    $receiverID,
    $currentUserID,
    $requestID,
    $notificationType,
    $notificationMessage
);

if (!$notification->execute()) {
    echo "notification_error";
    exit;
}

echo "success";

?>