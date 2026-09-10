<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Account - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="account-container">


        <!-- =========================
             PROFILE SECTION
        ========================== -->

<section class="account-profile">

    <h2 class="profile-name">
        Ash
    </h2>


    <div class="profile-picture">
        A
    </div>


    <h1>
        @ashmovies
    </h1>


    <a
        href="editProfile.php"
        class="edit-profile-btn"
    >
        Edit Profile
    </a>

</section>



        <!-- =========================
             ACCOUNT NAVIGATION
        ========================== -->

        <nav class="account-nav">

            <a href="watched.php">
                Watched
            </a>

            <a href="reviews.php">
                Reviews
            </a>

            <a href="watchlist.php">
                Watchlist
            </a>

        </nav>



        <!-- =========================
             TOP 5 SECTION
        ========================== -->

        <section class="top-five-section">


            <div class="top-five-heading">

                <h2>
                    @ashmovies's Top 5 Movies/TV Shows of All Time
                </h2>


                <a
                    href="editTop5.php"
                    class="edit-top-five-btn"
                >
                    EDIT
                </a>

            </div>



            <!-- =========================
                 TOP 5 MOVIES
            ========================== -->

            <div class="top-five-list">


                <!-- Movie 1 -->

                <div class="top-five-card">

                    <div class="movie-poster">
                        1
                    </div>

                    <h3>
                        BirdBox
                    </h3>

                </div>



                <!-- Movie 2 -->

                <div class="top-five-card">

                    <div class="movie-poster">
                        2
                    </div>

                    <h3>
                        I Will Find you
                    </h3>

                </div>



                <!-- Movie 3 -->

                <div class="top-five-card">

                    <div class="movie-poster">
                        3
                    </div>

                    <h3>
                        The 5<sup>th</sup> Wave
                    </h3>

                </div>



                <!-- Movie 4 -->

                <div class="top-five-card">

                    <div class="movie-poster">
                        4
                    </div>

                    <h3>
                        Until Dawn
                    </h3>

                </div>



                <!-- Movie 5 -->

                <div class="top-five-card">

                    <div class="movie-poster">
                        5
                    </div>

                    <h3>
                        People We Meet On Vacation
                    </h3>

                </div>


            </div>


        </section>


    </main>


</body>

</html>