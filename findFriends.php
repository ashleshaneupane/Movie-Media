<?php
include 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find Friends - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

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

                <h1>Find Friends</h1>

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

                <h2>People You May Know</h2>


                <!-- =========================
                     USER 1
                ========================== -->

                <div class="friend-card">

                    <div class="friend-profile">
                        A
                    </div>


                    <div class="friend-info">

                        <h3>Ash</h3>

                        <p>@ash001</p>

                        <span>
                            180 movies watched
                        </span>

                    </div>


                    <button
                        type="button"
                        class="add-friend-btn"
                    >
                        Add Friend
                    </button>

                </div>



                <!-- =========================
                     USER 2
                ========================== -->

                <div class="friend-card">

                    <div class="friend-profile">
                        M
                    </div>


                    <div class="friend-info">

                        <h3>Maya</h3>

                        <p>@mayamovies</p>

                        <span>
                            87 movies watched
                        </span>

                    </div>


                    <button
                        type="button"
                        class="add-friend-btn"
                    >
                        Add Friend
                    </button>

                </div>



                <!-- =========================
                     USER 3
                ========================== -->

                <div class="friend-card">

                    <div class="friend-profile">
                        S
                    </div>


                    <div class="friend-info">

                        <h3>Sarah</h3>

                        <p>@sarahmovies</p>

                        <span>
                            103 movies watched
                        </span>

                    </div>


                    <button
                        type="button"
                        class="add-friend-btn"
                    >
                        Add Friend
                    </button>

                </div>



                <!-- =========================
                     USER 4
                ========================== -->

                <div class="friend-card">

                    <div class="friend-profile">
                        D
                    </div>


                    <div class="friend-info">

                        <h3>Dev</h3>

                        <p>@devmovies</p>

                        <span>
                            605 movies watched
                        </span>

                    </div>


                    <button
                        type="button"
                        class="add-friend-btn"
                    >
                        Add Friend
                    </button>

                </div>


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



                <!-- =========================
                     REQUEST 1
                ========================== -->

                <div class="friend-request">

                    <div class="request-profile">
                        A
                    </div>


                    <div class="request-info">

                        <strong>Anne</strong>

                        <span>
                            @annemovies
                        </span>


                        <div class="request-actions">

                            <button
                                type="button"
                                class="accept-request"
                            >
                                Accept
                            </button>


                            <button
                                type="button"
                                class="decline-request"
                            >
                                Decline
                            </button>

                        </div>

                    </div>

                </div>



                <!-- =========================
                     REQUEST 2
                ========================== -->

                <div class="friend-request">

                    <div class="request-profile">
                        M
                    </div>


                    <div class="request-info">

                        <strong>Mike</strong>

                        <span>
                            @mike
                        </span>


                        <div class="request-actions">

                            <button
                                type="button"
                                class="accept-request"
                            >
                                Accept
                            </button>


                            <button
                                type="button"
                                class="decline-request"
                            >
                                Decline
                            </button>

                        </div>

                    </div>

                </div>


            </div>


        </aside>


    </main>



    <!-- =========================
         JAVASCRIPT
    ========================== -->

    <script>


        /*
         * ADD FRIEND
         */

        const addFriendButtons =
            document.querySelectorAll(".add-friend-btn");


        addFriendButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    button.textContent =
                        "Request Sent";

                    button.classList.add(
                        "request-sent"
                    );

                    button.disabled = true;

                }
            );

        });



        /*
         * ACCEPT FRIEND REQUEST
         */

        const acceptButtons =
            document.querySelectorAll(
                ".accept-request"
            );


        acceptButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    const request =
                        button.closest(
                            ".friend-request"
                        );


                    request.remove();

                }
            );

        });



        /*
         * DECLINE FRIEND REQUEST
         */

        const declineButtons =
            document.querySelectorAll(
                ".decline-request"
            );


        declineButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    const request =
                        button.closest(
                            ".friend-request"
                        );


                    request.remove();

                }
            );

        });



        /*
         * SEARCH FRIENDS
         */

        const searchInput =
            document.getElementById(
                "friendSearch"
            );


        const friendSearchBtn =
            document.getElementById(
                "friendSearchBtn"
            );


        friendSearchBtn.addEventListener(
            "click",
            function() {

                const searchValue =
                    searchInput.value
                    .trim()
                    .toLowerCase();


                const friendCards =
                    document.querySelectorAll(
                        ".friend-card"
                    );


                friendCards.forEach(
                    function(card) {

                        const name =
                            card.querySelector(
                                "h3"
                            )
                            .textContent
                            .toLowerCase();


                        const username =
                            card.querySelector(
                                "p"
                            )
                            .textContent
                            .toLowerCase();


                        if (
                            name.includes(searchValue) ||
                            username.includes(searchValue)
                        ) {

                            card.style.display =
                                "flex";

                        } else {

                            card.style.display =
                                "none";

                        }

                    }
                );

            }
        );


    </script>


</body>

</html>