<?php

include 'includes/auth.php';
include 'includes/config.php';

$currentUserID = $_SESSION["userID"];


/* =========================
   CHECK USER ID
========================= */

if (
    !isset($_GET["user"]) ||
    !is_numeric($_GET["user"])
) {
    die("Invalid user.");
}

$profileUserID = (int) $_GET["user"];


/* =========================
   GET USER
========================= */

$userQuery = $conn->prepare(
    "SELECT
        userID,
        username,
        name,
        profilePicture,
        bio,
        favoriteGenre
     FROM Users
     WHERE userID = ?"
);

$userQuery->bind_param(
    "i",
    $profileUserID
);

$userQuery->execute();

$userResult =
    $userQuery
    ->get_result();

$profileUser =
    $userResult->fetch_assoc();


if (!$profileUser) {
    die("User not found.");
}


/* =========================
   WATCHED COUNT
========================= */

$watchedQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM Watched
     WHERE userID = ?"
);

$watchedQuery->bind_param(
    "i",
    $profileUserID
);

$watchedQuery->execute();

$watchedCount =
    $watchedQuery
    ->get_result()
    ->fetch_assoc()["total"];


/* =========================
   WATCHED MOVIES
========================= */

$watchedMoviesQuery = $conn->prepare(
    "SELECT
        movie.movieID,
        movie.title,
        movie.poster,
        Watched.watchedDate
     FROM Watched
     INNER JOIN movie
        ON Watched.movieID = movie.movieID
     WHERE Watched.userID = ?
     ORDER BY Watched.watchedDate DESC"
);

$watchedMoviesQuery->bind_param(
    "i",
    $profileUserID
);

$watchedMoviesQuery->execute();

$watchedMoviesResult =
    $watchedMoviesQuery
    ->get_result();


/* =========================
   WATCHLIST COUNT
========================= */

$watchlistQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM Watchlist
     WHERE userID = ?"
);

$watchlistQuery->bind_param(
    "i",
    $profileUserID
);

$watchlistQuery->execute();

$watchlistCount =
    $watchlistQuery
    ->get_result()
    ->fetch_assoc()["total"];


/* =========================
   REVIEW COUNT + AVERAGE
========================= */

$reviewQuery = $conn->prepare(
    "SELECT
        COUNT(*) AS total,
        AVG(rating) AS averageRating
     FROM Review
     WHERE userID = ?"
);

$reviewQuery->bind_param(
    "i",
    $profileUserID
);

$reviewQuery->execute();

$reviewStats =
    $reviewQuery
    ->get_result()
    ->fetch_assoc();

$reviewCount =
    $reviewStats["total"];

$averageRating =
    $reviewStats["averageRating"];


/* =========================
   POST COUNT
========================= */

$postQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM Post
     WHERE userID = ?"
);

$postQuery->bind_param(
    "i",
    $profileUserID
);

$postQuery->execute();

$postCount =
    $postQuery
    ->get_result()
    ->fetch_assoc()["total"];


/* =========================
   FRIEND COUNT
========================= */

$friendCount = 0;


/* =========================
   TOP 5 MOVIES
========================= */

$topMovieQuery = $conn->prepare(
    "SELECT
        movie.title,
        movie.poster
     FROM UserTopMovies
     INNER JOIN movie
        ON UserTopMovies.movieID = movie.movieID
     WHERE UserTopMovies.userID = ?
     ORDER BY UserTopMovies.position ASC
     LIMIT 5"
);

$topMovieQuery->bind_param(
    "i",
    $profileUserID
);

$topMovieQuery->execute();

$topMovieResult =
    $topMovieQuery
    ->get_result();


/* =========================
   FAVORITE GENRES
========================= */

$favoriteGenres = [];

if (
    !empty(
        $profileUser["favoriteGenre"]
    )
) {

    $favoriteGenres =
        array_filter(
            array_map(
                "trim",
                explode(
                    ",",
                    $profileUser["favoriteGenre"]
                )
            )
        );

}


/* =========================
   PROFILE IMAGE
========================= */

$profilePicture =
    !empty(
        $profileUser["profilePicture"]
    )
    ? $profileUser["profilePicture"]
    : "";


/* =========================
   OWN PROFILE
========================= */

$isOwnProfile =
    $currentUserID === $profileUserID;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php
        echo htmlspecialchars(
            $profileUser["name"]
            ?: $profileUser["username"]
        );
        ?>
        - Movie Media
    </title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

    <style>

        /* =========================
           PUBLIC PROFILE
        ========================= */

        .public-profile-container {
            width: 100%;
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 25px;
            box-sizing: border-box;
        }


        .public-profile-card {
            background:
                linear-gradient(
                    145deg,
                    #171717,
                    #21172b,
                    #171717
                );

            border: 1px solid #49335f;
            border-radius: 20px;

            padding: 35px;

            box-sizing: border-box;

            box-shadow:
                0 15px 40px rgba(
                    0,
                    0,
                    0,
                    0.35
                );
        }


        /* =========================
           PROFILE HEADER
        ========================= */

        .public-profile-top {
            display: flex;
            align-items: center;

            gap: 28px;

            padding-bottom: 30px;
        }


        .public-profile-image {
    width: 125px;
    height: 125px;

    min-width: 125px;

    border-radius: 50%;

    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #ff7a00;

    color: white;

    font-size: 45px;
    font-weight: bold;

    border: 4px solid #ff9f1c;

    box-shadow:
        0 0 25px rgba(
            255,
            122,
            0,
            0.3
        );
}


        .public-profile-image img {
            width: 100%;
            height: 100%;

            object-fit: cover;

            display: block;
        }


        .public-profile-info {
            min-width: 0;
        }


        .public-profile-info h1 {
            margin: 0 0 5px;

            color: white;

            font-size: 32px;
        }


        .public-profile-username {
            margin: 0 0 14px;

            color: #b88cff;

            font-size: 14px;
        }


        .public-profile-bio {
            margin: 0;

            color: #ddd;

            line-height: 1.6;

            max-width: 650px;
        }


        /* =========================
           STATS
        ========================= */

        .public-profile-stats {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 15px;

            margin-bottom: 25px;
        }


        .public-profile-stat {
            background:
                linear-gradient(
                    135deg,
                    #252033,
                    #1d1d1d
                );

            border: 1px solid #39304b;

            border-radius: 13px;

            padding: 18px;

            text-align: center;
        }


        .public-profile-stat strong {
            display: block;

            color: #ff9f1c;

            font-size: 24px;

            margin-bottom: 5px;
        }


        .public-profile-stat span {
            color: #aaa;

            font-size: 12px;
        }


        /* =========================
           SECTIONS
        ========================= */

        .public-profile-section {
            border-top: 1px solid #3a3045;

            padding-top: 25px;

            margin-top: 25px;
        }


        .public-profile-section h2 {
            margin: 0 0 18px;

            color: white;

            font-size: 20px;
        }


        /* =========================
           MOVIE STATS
        ========================= */

        .public-movie-stats {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 15px;
        }


        .public-movie-stat {
            background: #202020;

            border-radius: 12px;

            padding: 16px;

            border-left:
                3px solid #ff7a00;
        }


        .public-movie-stat:nth-child(2) {
            border-left-color:
                #b84cff;
        }


        .public-movie-stat:nth-child(3) {
            border-left-color:
                #36cfff;
        }


        .public-movie-stat span {
            display: block;

            color: #888;

            font-size: 12px;

            margin-bottom: 6px;
        }


        .public-movie-stat strong {
            color: white;

            font-size: 20px;
        }


        /* =========================
           GENRES
        ========================= */

        .public-genre-list {
            display: flex;

            flex-wrap: wrap;

            gap: 9px;
        }


        .public-genre-list span {
            padding: 8px 13px;

            border-radius: 18px;

            background:
                linear-gradient(
                    135deg,
                    #37234d,
                    #27204a
                );

            border:
                1px solid #67468a;

            color: #d9c6ff;

            font-size: 12px;
        }


        /* =========================
           TOP 5
        ========================= */

        .public-top-movies {
            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 15px;

            margin: 0;

            padding: 0;

            list-style: none;
        }


        .public-top-movies li {
            background: #202020;

            border-radius: 10px;

            overflow: hidden;

            color: white;

            font-size: 13px;
        }


        .public-top-movies img {
            width: 100%;

            height: 180px;

            object-fit: cover;

            display: block;
        }


        .public-top-movie-title {
            padding: 10px;

            line-height: 1.3;
        }


        /* =========================
           WATCHED MOVIES
        ========================= */

        .watched-movies-grid {
            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 15px;
        }


        .watched-movie-card {
            background: #202020;

            border-radius: 10px;

            overflow: hidden;

            border: 1px solid #333;

            transition:
                transform 0.2s ease,
                border-color 0.2s ease;
        }


        .watched-movie-card:hover {
            transform:
                translateY(-4px);

            border-color:
                #ff7a00;
        }


        .watched-movie-card img {
            width: 100%;

            height: 190px;

            object-fit: cover;

            display: block;
        }


        .watched-movie-info {
            padding: 10px;
        }


        .watched-movie-info h3 {
            margin: 0 0 5px;

            color: white;

            font-size: 13px;

            line-height: 1.3;
        }


        .watched-movie-info p {
            margin: 0;

            color: #888;

            font-size: 10px;
        }


        .no-watched {
            color: #888;

            font-size: 13px;
        }


        /* =========================
           BACK BUTTON
        ========================= */

        .back-home-btn {
            display: inline-block;

            margin-bottom: 20px;

            padding: 9px 16px;

            border-radius: 18px;

            background:
                linear-gradient(
                    135deg,
                    #ff7a00,
                    #ff4d6d
                );

            color: white;

            text-decoration: none;

            font-size: 12px;

            font-weight: bold;
        }


        .back-home-btn:hover {
            opacity: 0.9;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 800px) {

            .public-top-movies,
            .watched-movies-grid {
                grid-template-columns:
                    repeat(3, 1fr);
            }

        }


        @media (max-width: 600px) {

            .public-profile-top {
                flex-direction: column;

                text-align: center;
            }


            .public-profile-stats,
            .public-movie-stats {
                grid-template-columns: 1fr;
            }


            .public-top-movies,
            .watched-movies-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

    </style>

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="public-profile-container">


    <a
        href="home.php"
        class="back-home-btn"
    >
        ← Back to Home
    </a>


    <div class="public-profile-card">


        <!-- =========================
             PROFILE HEADER
        ========================== -->

        <div class="public-profile-top">


            <div class="public-profile-image">

                <?php if (
                    $profilePicture !== ""
                ): ?>

                    <img
                        src="<?php
                            echo htmlspecialchars(
                                $profilePicture
                            );
                        ?>"
                        alt="Profile Picture"
                    >

                <?php else: ?>

                    <?php

                    echo strtoupper(
                        substr(
                            $profileUser["name"]
                            ?: $profileUser["username"],
                            0,
                            1
                        )
                    );

                    ?>

                <?php endif; ?>

            </div>


            <div class="public-profile-info">

                <h1>

                    <?php

                    echo htmlspecialchars(
                        $profileUser["name"]
                        ?: $profileUser["username"]
                    );

                    ?>

                </h1>


                <p class="public-profile-username">

                    @<?php

                    echo htmlspecialchars(
                        $profileUser["username"]
                    );

                    ?>

                </p>


                <p class="public-profile-bio">

                    <?php

                    if (
                        !empty(
                            $profileUser["bio"]
                        )
                    ) {

                        echo htmlspecialchars(
                            $profileUser["bio"]
                        );

                    } else {

                        echo "Movie lover, escaping reality.";

                    }

                    ?>

                </p>

            </div>

        </div>



        <!-- =========================
             PROFILE STATS
        ========================== -->

        <div class="public-profile-stats">


            <div class="public-profile-stat">

                <strong>
                    <?php echo $postCount; ?>
                </strong>

                <span>
                    Posts
                </span>

            </div>


            <div class="public-profile-stat">

                <strong>
                    <?php echo $watchedCount; ?>
                </strong>

                <span>
                    Watched
                </span>

            </div>


            <div class="public-profile-stat">

                <strong>
                    <?php echo $friendCount; ?>
                </strong>

                <span>
                    Friends
                </span>

            </div>


        </div>



        <!-- =========================
             MOVIE STATS
        ========================== -->

        <div class="public-profile-section">

            <h2>
                🎬 Movie Stats
            </h2>


            <div class="public-movie-stats">


                <div class="public-movie-stat">

                    <span>
                        Watchlist
                    </span>

                    <strong>
                        <?php echo $watchlistCount; ?>
                    </strong>

                </div>


                <div class="public-movie-stat">

                    <span>
                        Reviews
                    </span>

                    <strong>
                        <?php echo $reviewCount; ?>
                    </strong>

                </div>


                <div class="public-movie-stat">

                    <span>
                        Average Rating
                    </span>

                    <strong>

                        <?php

                        echo $averageRating === null
                            ? "0.0"
                            : number_format(
                                (float) $averageRating,
                                1
                            );

                        ?>

                    </strong>

                </div>


            </div>

        </div>



        <!-- =========================
             FAVORITE GENRES
        ========================== -->

        <div class="public-profile-section">

            <h2>
                🎨 Favorite Genres
            </h2>


            <div class="public-genre-list">

                <?php if (
                    !empty($favoriteGenres)
                ): ?>

                    <?php foreach (
                        $favoriteGenres as $genre
                    ): ?>

                        <span>

                            <?php

                            echo htmlspecialchars(
                                $genre
                            );

                            ?>

                        </span>

                    <?php endforeach; ?>

                <?php else: ?>

                    <span>
                        No genres selected
                    </span>

                <?php endif; ?>

            </div>

        </div>



        <!-- =========================
             TOP 5
        ========================== -->

        <div class="public-profile-section">

            <h2>
                ⭐ Top 5 Movies
            </h2>


            <?php if (
                $topMovieResult->num_rows > 0
            ): ?>

                <div class="public-top-movies">

                    <?php while (
                        $topMovie =
                        $topMovieResult->fetch_assoc()
                    ): ?>

                        <div>

                            <?php if (
                                !empty(
                                    $topMovie["poster"]
                                )
                            ): ?>

                                <img
                                    src="<?php
                                        echo htmlspecialchars(
                                            $topMovie["poster"]
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $topMovie["title"]
                                        );
                                    ?>"
                                >

                            <?php endif; ?>


                            <div
                                class="public-top-movie-title"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $topMovie["title"]
                                );

                                ?>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            <?php else: ?>

                <p class="no-watched">
                    No top movies added yet.
                </p>

            <?php endif; ?>

        </div>



        <!-- =========================
             WATCHED MOVIES
        ========================== -->

        <div class="public-profile-section">

            <h2>
                🍿 Watched Movies
            </h2>


            <?php if (
                $watchedMoviesResult->num_rows > 0
            ): ?>

                <div class="watched-movies-grid">

                    <?php while (
                        $watchedMovie =
                        $watchedMoviesResult->fetch_assoc()
                    ): ?>

                        <div
                            class="watched-movie-card"
                        >

                            <?php if (
                                !empty(
                                    $watchedMovie["poster"]
                                )
                            ): ?>

                                <img
                                    src="<?php
                                        echo htmlspecialchars(
                                            $watchedMovie[
                                                "poster"
                                            ]
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $watchedMovie[
                                                "title"
                                            ]
                                        );
                                    ?>"
                                >

                            <?php endif; ?>


                            <div
                                class="watched-movie-info"
                            >

                                <h3>

                                    <?php

                                    echo htmlspecialchars(
                                        $watchedMovie[
                                            "title"
                                        ]
                                    );

                                    ?>

                                </h3>


                                <?php if (
                                    !empty(
                                        $watchedMovie[
                                            "watchedDate"
                                        ]
                                    )
                                ): ?>

                                    <p>

                                        Watched:

                                        <?php

                                        echo date(
                                            "M d, Y",
                                            strtotime(
                                                $watchedMovie[
                                                    "watchedDate"
                                                ]
                                            )
                                        );

                                        ?>

                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            <?php else: ?>

                <p class="no-watched">
                    No watched movies yet.
                </p>

            <?php endif; ?>

        </div>


    </div>

</main>


</body>

</html>