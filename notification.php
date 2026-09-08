<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notifications - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="notification-container">


        <!-- =========================
             PAGE HEADER
        ========================== -->

        <div class="notification-header">

            <h1 id="noti-title">Notifications</h1>

            <button
                type="button"
                id="markAllRead"
            >
                Mark all as read
            </button>

        </div>



        <!-- =========================
             NOTIFICATIONS
        ========================== -->

        <section class="notification-list">


            <!-- =========================
                 NOTIFICATION 1
            ========================== -->

            <div class="notification unread">

                <div class="notification-profile">
                    A
                </div>


                <div class="notification-content">

                    <p>
                        <strong>@alex</strong>
                        liked your post
                    </p>

                    <span>
                        "Just watched BirdBox..."
                    </span>

                    <small>
                        2 minutes ago
                    </small>

                </div>


                <span class="unread-dot"></span>

            </div>



            <!-- =========================
                 NOTIFICATION 2
            ========================== -->

            <div class="notification unread">

                <div class="notification-profile">
                    M
                </div>


                <div class="notification-content">

                    <p>
                        <strong>@mike</strong>
                        commented on your post
                    </p>

                    <span>
                        "That ending was insane!"
                    </span>

                    <small>
                        15 minutes ago
                    </small>

                </div>


                <span class="unread-dot"></span>

            </div>



            <!-- =========================
                 NOTIFICATION 3
            ========================== -->

            <div class="notification unread">

                <div class="notification-profile">
                    A
                </div>


                <div class="notification-content">

                    <p>
                        <strong>@ash</strong>
                        started following you
                    </p>

                    <small>
                        1 hour ago
                    </small>

                </div>


                <span class="unread-dot"></span>

            </div>



            <!-- =========================
                 NOTIFICATION 4
            ========================== -->

            <div class="notification unread">

                <div class="notification-profile">
                    J
                </div>


                <div class="notification-content">

                    <p>
                        <strong>@jack</strong>
                        sent you a friend request
                    </p>


                    <div class="friend-actions">

                        <button
                            type="button"
                            class="accept-btn"
                        >
                            Accept
                        </button>


                        <button
                            type="button"
                            class="decline-btn"
                        >
                            Decline
                        </button>

                    </div>


                    <small>
                        3 hours ago
                    </small>

                </div>


                <span class="unread-dot"></span>

            </div>



            <!-- =========================
                 NOTIFICATION 5
            ========================== -->

            <div class="notification">

                <div class="notification-profile">
                    🎬
                </div>


                <div class="notification-content">

                    <p>
                        Your review of
                        <strong>"I Will Find You"</strong>
                        received 5 likes
                    </p>

                    <small>
                        Yesterday
                    </small>

                </div>

            </div>



        </section>



        <!-- =========================
             EMPTY STATE
        ========================== -->

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
                When people interact with your posts,
                you'll see it here.
            </p>

        </section>


    </main>



    <!-- =========================
         JAVASCRIPT
    ========================== -->

    <script>


        /*
         * Mark individual notification
         * as read when clicked.
         */

        const notifications =
            document.querySelectorAll(".notification");


        notifications.forEach(function(notification) {

            notification.addEventListener(
                "click",
                function(event) {

                    /*
                     * Don't mark the notification
                     * when clicking Accept/Decline.
                     */

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



        /*
         * Mark all notifications as read.
         */

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



        /*
         * Accept friend request.
         */

        const acceptButtons =
            document.querySelectorAll(".accept-btn");


        acceptButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    const notification =
                        button.closest(".notification");


                    notification.remove();

                    alert(
                        "Friend request accepted!"
                    );

                }
            );

        });



        /*
         * Decline friend request.
         */

        const declineButtons =
            document.querySelectorAll(".decline-btn");


        declineButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    const notification =
                        button.closest(".notification");


                    notification.remove();

                    alert(
                        "Friend request declined."
                    );

                }
            );

        });

    </script>


</body>

</html>