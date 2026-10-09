<?php

include 'includes/auth.php';
include 'includes/config.php';

$currentUserID = (int) $_SESSION["userID"];


/* GET NOTIFICATIONS */

$notificationQuery = $conn->prepare(
    "SELECT
        Notification.notificationID,
        Notification.senderID,
        Notification.requestID,
        Notification.type,
        Notification.message,
        Notification.isRead,
        Notification.createdAt,
        Users.name,
        Users.username,
        Users.profilePicture
     FROM Notification
     LEFT JOIN Users
        ON Notification.senderID = Users.userID
     WHERE Notification.userID = ?
     AND Notification.isRead = 0
     ORDER BY Notification.createdAt DESC"
);

$notificationQuery->bind_param(
    "i",
    $currentUserID
);

$notificationQuery->execute();

$notifications = $notificationQuery->get_result();


/* RELATIVE TIME */

function timeAgo($datetime)
{
    $time = strtotime($datetime);

    $difference = time() - $time;


    if ($difference < 60) {
        return "Just now";
    }


    if ($difference < 3600) {

        $minutes = floor($difference / 60);

        return $minutes . " minute" .
            ($minutes != 1 ? "s" : "") .
            " ago";
    }


    if ($difference < 86400) {

        $hours = floor($difference / 3600);

        return $hours . " hour" .
            ($hours != 1 ? "s" : "") .
            " ago";
    }


    if ($difference < 604800) {

        $days = floor($difference / 86400);

        return $days . " day" .
            ($days != 1 ? "s" : "") .
            " ago";
    }


    return date("M j, Y", $time);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Notifications - Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="notification-container">


    <div class="notification-header">

        <h1 id="noti-title">
            Notifications
        </h1>

        <button
            type="button"
            id="markAllRead"
        >
            Mark all as read
        </button>

    </div>


    <section class="notification-list">


        <?php if ($notifications->num_rows > 0): ?>


            <?php while ($notification = $notifications->fetch_assoc()): ?>


                <div
                    class="notification <?php echo $notification["isRead"] == 0 ? "unread" : ""; ?>"
                    data-notification-id="<?php echo $notification["notificationID"]; ?>"
                >


                    <div class="notification-profile">


                        <?php if (!empty($notification["profilePicture"])): ?>


                            <img
                                src="<?php echo htmlspecialchars($notification["profilePicture"]); ?>"
                                alt="<?php echo htmlspecialchars($notification["name"] ?? "User"); ?>"
                            >


                        <?php else: ?>


                            <?php

                            $initial = !empty($notification["name"])
                                ? strtoupper(substr($notification["name"], 0, 1))
                                : "?";

                            ?>


                            <?php echo htmlspecialchars($initial); ?>


                        <?php endif; ?>


                    </div>


                    <div class="notification-content">


                        <p>


                            <?php if (!empty($notification["username"])): ?>


                                <strong>
                                    @<?php echo htmlspecialchars($notification["username"]); ?>
                                </strong>


                            <?php endif; ?>


                            <?php echo htmlspecialchars($notification["message"]); ?>


                        </p>


                        <?php if ($notification["type"] === "friend_request"): ?>


                            <div class="friend-actions">


                                <button
                                    type="button"
                                    class="accept-btn"
                                    data-request-id="<?php echo $notification["requestID"]; ?>"
                                >
                                    Accept
                                </button>


                                <button
                                    type="button"
                                    class="decline-btn"
                                    data-request-id="<?php echo $notification["requestID"]; ?>"
                                >
                                    Decline
                                </button>


                            </div>


                        <?php endif; ?>


                        <small>
                            <?php echo timeAgo($notification["createdAt"]); ?>
                        </small>


                    </div>


                    <?php if ($notification["isRead"] == 0): ?>


                        <span class="unread-dot"></span>


                    <?php endif; ?>


                </div>


            <?php endwhile; ?>


        <?php endif; ?>


    </section>


    <?php if ($notifications->num_rows === 0): ?>


        <section
            class="empty-notifications"
            id="emptyNotifications"
        >

            <div class="empty-icon">
                🔔
            </div>

            <h2>
                No notifications yet
            </h2>

            <p>
                When people interact with your posts, you'll see it here.
            </p>

        </section>


    <?php endif; ?>


</main>


<script>


/* =========================
   MARK NOTIFICATION READ
========================= */

const notifications =
    document.querySelectorAll(".notification");


notifications.forEach(function(notification) {

    notification.addEventListener(
        "click",
        function(event) {

            if (
                event.target.classList.contains("accept-btn") ||
                event.target.classList.contains("decline-btn")
            ) {
                return;
            }


            notification.classList.remove("unread");


            const dot =
                notification.querySelector(".unread-dot");


            if (dot) {
                dot.remove();
            }

        }
    );

});


/* =========================
   MARK ALL AS READ
========================= */

const markAllRead =
    document.getElementById("markAllRead");


markAllRead.addEventListener(
    "click",
    function() {

        notifications.forEach(
            function(notification) {

                notification.classList.remove("unread");


                const dot =
                    notification.querySelector(".unread-dot");


                if (dot) {
                    dot.remove();
                }

            }
        );

    }
);


/* =========================
   ACCEPT FRIEND REQUEST
========================= */

const acceptButtons =
    document.querySelectorAll(".accept-btn");


acceptButtons.forEach(function(button) {

    button.addEventListener(
        "click",
        function() {

            const notification =
                button.closest(".notification");


            const requestID =
                button.dataset.requestId;


            const formData =
                new FormData();


            formData.append(
                "requestID",
                requestID
            );


            formData.append(
                "action",
                "accept"
            );


            fetch(
                "respondFriendRequest.php",
                {
                    method: "POST",
                    body: formData
                }
            )

            .then(function(response) {
                return response.text();
            })

            .then(function(result) {

                result = result.trim();

                console.log(
                    "Accept friend request result:",
                    result
                );


                if (
                    result === "accepted" ||
                    result === "not_found"
                ) {

                    notification.remove();

                }

                else {

                    alert(
                        "Something went wrong."
                    );

                }

            })

            .catch(function(error) {

                console.log(
                    "Accept error:",
                    error
                );

                alert(
                    "Something went wrong."
                );

            });

        }
    );

});

/* =========================
   DECLINE FRIEND REQUEST
========================= */

const declineButtons =
    document.querySelectorAll(".decline-btn");


declineButtons.forEach(function(button) {

    button.addEventListener(
        "click",
        function() {

            const notification =
                button.closest(".notification");


            const requestID =
                button.dataset.requestId;


            const formData =
                new FormData();


            formData.append(
                "requestID",
                requestID
            );


            formData.append(
                "action",
                "decline"
            );


            fetch(
                "respondFriendRequest.php",
                {
                    method: "POST",
                    body: formData
                }
            )

            .then(function(response) {
                return response.text();
            })

            .then(function(result) {

                if (result.trim() === "declined") {

                    notification.remove();

                    alert(
                        "Friend request declined."
                    );

                } else {

                    alert(
                        "Something went wrong."
                    );

                }

            });

        }
    );

});

</script>


</body>

</html>