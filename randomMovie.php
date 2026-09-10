<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Random Movie - Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="random-movie-container">


        <!-- BACK BUTTON -->

        <a
            href="search.php"
            class="back-button"
        >
            ←
        </a>


        <?php

        /*
         * TEMPORARY MOVIE DATA
         *
         * This is only being used until
         * the Movie Media database is created.
         */

        $movies = [

            [
                "title" => "Interstellar",
                "year" => "2014",
                "genre" => "Sci-Fi",
                "rating" => "8.7",
                "overview" => "A group of explorers travel through a wormhole in space in search of a new home for humanity."
            ],

            [
                "title" => "Inception",
                "year" => "2010",
                "genre" => "Thriller",
                "rating" => "8.8",
                "overview" => "A skilled thief who steals secrets through dreams is given a chance to erase his past."
            ],

            [
                "title" => "Oppenheimer",
                "year" => "2023",
                "genre" => "Drama",
                "rating" => "8.6",
                "overview" => "The story of the scientist who played a major role in the development of the atomic bomb."
            ],

            [
                "title" => "Avatar",
                "year" => "2009",
                "genre" => "Action",
                "rating" => "7.5",
                "overview" => "A marine becomes part of the world of Pandora and joins its inhabitants in their fight."
            ],

            [
                "title" => "The Notebook",
                "year" => "2004",
                "genre" => "Romance",
                "rating" => "7.8",
                "overview" => "A young couple fall deeply in love despite the obstacles that separate them."
            ],

            [
                "title" => "The Conjuring",
                "year" => "2013",
                "genre" => "Horror",
                "rating" => "7.5",
                "overview" => "Paranormal investigators help a family experiencing terrifying events in their new home."
            ]

        ];


        /*
         * Choose a random movie
         */

        $randomIndex =
            rand(
                0,
                count($movies) - 1
            );


        $movie =
            $movies[$randomIndex];

        ?>


        <!-- HEADING -->

        <div class="random-heading">

            <p class="random-label">
                MOVIE MEDIA
            </p>


            <h1>
                What should you watch?
            </h1>


            <p>
                Here's a random movie for you.
            </p>

        </div>



        <!-- RANDOM MOVIE CARD -->

        <section class="random-movie-card">


            <!-- POSTER -->

            <div class="random-movie-poster">

                <span>

                    <?php
                    echo htmlspecialchars(
                        $movie["title"]
                    );
                    ?>

                </span>

            </div>



            <!-- INFORMATION -->

            <div class="random-movie-info">


                <h2>

                    <?php
                    echo htmlspecialchars(
                        $movie["title"]
                    );
                    ?>

                </h2>



                <p class="random-movie-meta">

                    <?php
                    echo htmlspecialchars(
                        $movie["year"]
                    );
                    ?>

                    •

                    <?php
                    echo htmlspecialchars(
                        $movie["genre"]
                    );
                    ?>

                </p>



                <div class="random-movie-rating">

                    ⭐

                    <?php
                    echo htmlspecialchars(
                        $movie["rating"]
                    );
                    ?>

                    / 10

                </div>



                <p class="random-movie-overview">

                    <?php
                    echo htmlspecialchars(
                        $movie["overview"]
                    );
                    ?>

                </p>



                <!-- BUTTONS -->

                <div class="random-movie-actions">


                    <a
                        href="search.php"
                        class="random-view-btn"
                    >
                        Back to Search
                    </a>


                    <a
                        href="randomMovie.php"
                        class="random-again-btn"
                    >
                        🎲 Try Another
                    </a>


                </div>


            </div>


        </section>


    </main>


</body>

</html>