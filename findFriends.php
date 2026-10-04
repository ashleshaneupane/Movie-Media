<?php

include 'includes/auth.php';
include 'includes/config.php';

$currentUserID =
    (int) $_SESSION["userID"];


/* =========================
   LOAD USERS + RELATIONSHIP
========================= */

$userQuery = $conn->prepare(
"SELECT
    Users.userID,
    Users.name,
    Users.username,
    Users.profilePicture,
    FriendRequest.requestID,
    FriendRequest.senderID,
    FriendRequest.receiverID,
    FriendRequest.status
FROM Users
LEFT JOIN FriendRequest
ON (
    (FriendRequest.senderID = ? AND FriendRequest.receiverID = Users.userID)
    OR
    (FriendRequest.senderID = Users.userID AND FriendRequest.receiverID = ?)
)
WHERE Users.userID != ?
AND Users.role != 'admin'
AND NOT EXISTS (
    SELECT 1
    FROM FriendRequest AS AcceptedFriend
    WHERE AcceptedFriend.status = 'accepted'
    AND (
        (AcceptedFriend.senderID = ? AND AcceptedFriend.receiverID = Users.userID)
        OR
        (AcceptedFriend.senderID = Users.userID AND AcceptedFriend.receiverID = ?)
    )
)
ORDER BY Users.name ASC"
);

$userQuery->bind_param(
    "iiiii",
    $currentUserID,
    $currentUserID,
    $currentUserID,
    $currentUserID,
    $currentUserID
);

$userQuery->execute();
$users = $userQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Find Friends - Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="find-friends-container">


        <!-- =========================
             LEFT SIDE
        ========================== -->

        <section class="friends-main">


            <!-- PAGE TITLE -->

            <div class="friends-heading">

                <h1>
                    Find Friends
                </h1>

                <p>
                    Find movie lovers and connect with
                    people who share your taste.
                </p>

            </div>



            <!-- SEARCH -->

            <div class="friends-search">

                <input
                    type="text"
                    id="friendSearch"
                    placeholder="Search people..."
                >

                <button
                    type="button"
                    id="friendSearchBtn"
                >
                    Search
                </button>

            </div>



            <!-- PEOPLE YOU MAY KNOW -->

            <section class="suggested-friends">

                <h2>
                    People You May Know
                </h2>


                <?php if ($users->num_rows === 0): ?>

                    <p class="no-friends">
                        No other users found.
                    </p>

                <?php else: ?>


                    <?php while (
                        $user =
                            $users->fetch_assoc()
                    ): ?>


                        <?php

                        $buttonText =
                            "Add Friend";

                        $buttonClass =
                            "";

                        $buttonDisabled =
                            false;


                        if (
                            !empty(
                                $user["status"]
                            )
                        ) {


                            if (
                                $user["status"] ===
                                "accepted"
                            ) {

                                $buttonText =
                                    "Friends";

                                $buttonClass =
                                    "request-sent";

                                $buttonDisabled =
                                    true;

                            }


                            elseif (
                                $user["status"] ===
                                "pending"
                            ) {


                                if (
                                    (int)
                                    $user["senderID"]
                                    ===
                                    $currentUserID
                                ) {

                                    $buttonText =
                                        "Request Sent";

                                    $buttonClass =
                                        "request-sent";

                                    $buttonDisabled =
                                        true;

                                }

                                else {

                                    $buttonText =
                                        "Respond to Request";

                                }

                            }

                        }

                        ?>


                        <div
                            class="friend-card"
                            data-name="<?php
                                echo htmlspecialchars(
                                    $user["name"]
                                );
                            ?>"
                            data-username="<?php
                                echo htmlspecialchars(
                                    $user["username"]
                                );
                            ?>"
                        >


                            <div class="friend-profile">

                                <?php
                                if (
                                    !empty(
                                        $user[
                                            "profilePicture"
                                        ]
                                    )
                                ):
                                ?>

                                    <img
                                        src="<?php
                                            echo htmlspecialchars(
                                                $user[
                                                    "profilePicture"
                                                ]
                                            );
                                        ?>"
                                        alt="<?php
                                            echo htmlspecialchars(
                                                $user["name"]
                                            );
                                        ?>"
                                    >

                                <?php else: ?>

                                    <?php

                                    echo strtoupper(
                                        substr(
                                            $user["name"],
                                            0,
                                            1
                                        )
                                    );

                                    ?>

                                <?php endif; ?>

                            </div>



                            <div class="friend-info">

                                <h3>

                                    <?php
                                    echo htmlspecialchars(
                                        $user["name"]
                                    );
                                    ?>

                                </h3>


                                <p>

                                    @<?php
                                    echo htmlspecialchars(
                                        $user["username"]
                                    );
                                    ?>

                                </p>

                            </div>



                            <button
                                type="button"
                                class="add-friend-btn <?php
                                    echo $buttonClass;
                                ?>"
                                data-user-id="<?php
                                    echo (int)
                                        $user["userID"];
                                ?>"
                                <?php
                                echo $buttonDisabled
                                    ? "disabled"
                                    : "";
                                ?>
                            >

                                <?php
                                echo $buttonText;
                                ?>

                            </button>


                        </div>


                    <?php endwhile; ?>


                <?php endif; ?>


            </section>


        </section>


        <!-- =========================
             RIGHT SIDEBAR
        ========================== -->

        <aside class="friend-sidebar">


            <!-- FRIEND REQUESTS -->

            <div class="friend-requests-card">


                <h2>
                    Friend Requests
                </h2>


                <div id="friendRequestList">

                    <p class="no-friend-requests">
                        Loading requests...
                    </p>

                </div>


            </div>


        </aside>


    </main>

    <!-- =========================
         JAVASCRIPT
    ========================== -->

  <script>


/* =========================
   ADD FRIEND
========================= */

const addFriendButtons =
    document.querySelectorAll(
        ".add-friend-btn"
    );


addFriendButtons.forEach(
    function(button) {

        button.addEventListener(
            "click",
            function() {


                const receiverID =
                    button.dataset.userId;


                const formData =
                    new FormData();


                formData.append(
                    "receiverID",
                    receiverID
                );


                button.disabled =
                    true;


                fetch(
                    "sendFriendRequest.php",
                    {
                        method: "POST",
                        body: formData
                    }
                )

                .then(
                    function(response) {

                        return response.text();

                    }
                )

                .then(
                    function(result) {


                        result =
                            result.trim();


                        if (
                            result ===
                                "success" ||
                            result ===
                                "sent"
                        ) {

                            button.textContent =
                                "Request Sent";

                            button.classList.add(
                                "request-sent"
                            );

                        }


                        else if (
                            result ===
                                "friends"
                        ) {

                            button.textContent =
                                "Friends";

                            button.classList.add(
                                "request-sent"
                            );

                        }


                        else if (
                            result ===
                                "received"
                        ) {

                            button.textContent =
                                "Respond to Request";

                            button.disabled =
                                false;

                        }


                        else {

                            button.textContent =
                                "Add Friend";

                            button.disabled =
                                false;

                            alert(
                                "Could not send friend request."
                            );

                        }

                    }
                )

                .catch(
                    function() {

                        button.textContent =
                            "Add Friend";

                        button.disabled =
                            false;

                        alert(
                            "Could not send friend request."
                        );

                    }
                );

            }
        );

    }
);



/* =========================
   LOAD FRIEND REQUESTS
========================= */

const friendRequestList =
    document.getElementById(
        "friendRequestList"
    );


function loadFriendRequests() {

    fetch(
        "getFriendRequests.php"
    )

    .then(function(response) {

        return response.json();

    })

    .then(function(requests) {

        friendRequestList.innerHTML = "";


        if (requests.length === 0) {

            const emptyMessage =
                document.createElement("p");

            emptyMessage.className =
                "no-friend-requests";

            emptyMessage.textContent =
                "No friend requests.";

            friendRequestList.appendChild(
                emptyMessage
            );

            return;

        }


        requests.forEach(
            function(request) {

                const requestCard =
                    document.createElement("div");

                requestCard.className =
                    "friend-request";


                /* =========================
                   PROFILE
                ========================== */

                const profile =
                    document.createElement("div");

                profile.className =
                    "request-profile";


                if (
                    request.profilePicture
                ) {

                    const image =
                        document.createElement("img");

                    image.src =
                        request.profilePicture;

                    image.alt =
                        request.name;

                    profile.appendChild(
                        image
                    );

                }

                else {

                    profile.textContent =
                        request.name
                            .charAt(0)
                            .toUpperCase();

                }



                /* =========================
                   REQUEST INFO
                ========================== */

                const info =
                    document.createElement("div");

                info.className =
                    "request-info";


                const name =
                    document.createElement("strong");

                name.textContent =
                    request.name;


                const username =
                    document.createElement("span");

                username.textContent =
                    "@" + request.username;



                /* =========================
                   ACTIONS
                ========================== */

                const actions =
                    document.createElement("div");

                actions.className =
                    "request-actions";



                /* ACCEPT */

                const acceptButton =
                    document.createElement("button");

                acceptButton.type =
                    "button";

                acceptButton.className =
                    "accept-request";

                acceptButton.textContent =
                    "Accept";


                acceptButton.addEventListener(
                    "click",
                    function() {

                        respondToFriendRequest(
                            request.requestID,
                            "accept",
                            requestCard
                        );

                    }
                );



                /* DECLINE */

                const declineButton =
                    document.createElement("button");

                declineButton.type =
                    "button";

                declineButton.className =
                    "decline-request";

                declineButton.textContent =
                    "Decline";


                declineButton.addEventListener(
                    "click",
                    function() {

                        respondToFriendRequest(
                            request.requestID,
                            "decline",
                            requestCard
                        );

                    }
                );



                /* =========================
                   BUILD REQUEST CARD
                ========================== */

                actions.appendChild(
                    acceptButton
                );

                actions.appendChild(
                    declineButton
                );


                info.appendChild(
                    name
                );

                info.appendChild(
                    username
                );

                info.appendChild(
                    actions
                );


                requestCard.appendChild(
                    profile
                );

                requestCard.appendChild(
                    info
                );


                friendRequestList.appendChild(
                    requestCard
                );

            }
        );

    })

    .catch(function() {

        friendRequestList.innerHTML =
            "<p class='no-friend-requests'>" +
            "Could not load requests." +
            "</p>";

    });

}



/* =========================
   ACCEPT / DECLINE REQUEST
========================= */

function respondToFriendRequest(
    requestID,
    action,
    requestCard
) {


    const formData =
        new FormData();


    formData.append(
        "requestID",
        requestID
    );


    formData.append(
        "action",
        action
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


        result =
            result.trim();


        if (
            result === "accepted" ||
            result === "declined"
        ) {

            requestCard.remove();

        }

        else {

            alert(
                "Could not update friend request."
            );

        }

    })

    .catch(function() {

        alert(
            "Could not update friend request."
        );

    });

}



/* =========================
   START FRIEND REQUESTS
========================= */

loadFriendRequests();


/* =========================
   SEARCH FRIENDS
========================= */

const searchInput =
    document.getElementById(
        "friendSearch"
    );

const friendSearchBtn =
    document.getElementById(
        "friendSearchBtn"
    );

const suggestedFriends =
    document.querySelector(
        ".suggested-friends"
    );


function searchFriends() {

    const searchValue =
        searchInput.value
            .trim()
            .toLowerCase();

    const friendCards =
        document.querySelectorAll(
            ".friend-card"
        );

    let visibleCount = 0;


    friendCards.forEach(
        function(card) {

            const name =
                card.dataset.name
                    .toLowerCase();

            const username =
                card.dataset.username
                    .toLowerCase();


            if (
                name.includes(searchValue) ||
                username.includes(searchValue)
            ) {

                card.style.display =
                    "flex";

                visibleCount++;

            }

            else {

                card.style.display =
                    "none";

            }

        }
    );


    /* =========================
       NO RESULTS MESSAGE
    ========================= */

    let noResults =
        document.getElementById(
            "noFriendSearchResults"
        );


    if (
        searchValue !== "" &&
        visibleCount === 0
    ) {

        if (!noResults) {

            noResults =
                document.createElement("p");

            noResults.id =
                "noFriendSearchResults";

            noResults.className =
                "no-friends";

            noResults.textContent =
                "No people found.";

            suggestedFriends.appendChild(
                noResults
            );

        }

    }

    else {

        if (noResults) {

            noResults.remove();

        }

    }

}


/* SEARCH BUTTON */

friendSearchBtn.addEventListener(
    "click",
    searchFriends
);


/* SEARCH WHILE TYPING */

searchInput.addEventListener(
    "input",
    searchFriends
);


/* PRESS ENTER TO SEARCH */

searchInput.addEventListener(
    "keydown",
    function(event) {

        if (
            event.key === "Enter"
        ) {

            event.preventDefault();

            searchFriends();

        }

    }
);

</script>

</body>

</html>