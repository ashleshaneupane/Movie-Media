<?php

include 'includes/auth.php';
include 'includes/config.php';

$currentUserID = (int) $_SESSION["userID"];


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

$userResult = $userQuery->get_result();

$profileUser = $userResult->fetch_assoc();


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

$friendCountQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM FriendRequest
     WHERE status = 'accepted'
     AND (
        senderID = ?
        OR receiverID = ?
     )"
);

$friendCountQuery->bind_param(
    "ii",
    $profileUserID,
    $profileUserID
);

$friendCountQuery->execute();

$friendCount =
    $friendCountQuery
    ->get_result()
    ->fetch_assoc()["total"];


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
    !empty($profileUser["favoriteGenre"])
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
    !empty($profileUser["profilePicture"])
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
   PUBLIC PROFILE CONTAINER
========================= */

.public-profile-container {
    width: 100%;
    max-width: 1180px;

    margin: 30px auto 60px;

    padding: 0 25px;

    box-sizing: border-box;
}


/* =========================
   BACK BUTTON
========================= */

.back-home-btn {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    margin-bottom: 20px;

    padding: 9px 15px;

    border-radius: 7px;

    background:
        rgba(255,255,255,0.06);

    border:
        1px solid rgba(255,255,255,0.12);

    color: #ddd;

    text-decoration: none;

    font-size: 13px;

    font-weight: 500;

    transition:
        background 0.2s ease,
        border-color 0.2s ease,
        color 0.2s ease;
}

.back-home-btn:hover {
    background:
        rgba(255,255,255,0.10);

    border-color:
        rgba(255,159,28,0.45);

    color: #fff;
}


/* =========================
   MAIN PROFILE CARD
========================= */

.public-profile-card {
    position: relative;

    background:
        linear-gradient(
            180deg,
            rgba(27,27,27,0.98),
            rgba(13,13,13,0.99)
        );

    border:
        1px solid rgba(255,159,28,0.18);

    border-radius: 14px;

    padding: 32px;

    box-sizing: border-box;

    box-shadow:
        0 20px 60px rgba(0,0,0,0.50);

    overflow: hidden;
}


/* =========================
   CINEMATIC ORANGE GLOW
========================= */

.public-profile-card::before {
    content: "";

    position: absolute;

    top: -190px;

    right: -130px;

    width: 450px;

    height: 450px;

    background:
        radial-gradient(
            circle,
            rgba(255,140,0,0.16),
            rgba(255,140,0,0.05) 35%,
            transparent 70%
        );

    pointer-events: none;
}


/* =========================
   PROFILE HEADER
========================= */

.public-profile-top {
    position: relative;

    display: flex;

    align-items: center;

    gap: 25px;

    padding-bottom: 30px;

    border-bottom:
        1px solid rgba(255,255,255,0.08);
}


/* =========================
   PROFILE IMAGE
========================= */

.public-profile-image {
    width: 145px;
    height: 145px;
    min-width: 145px;
    min-height: 145px;

    border-radius: 50%;
    overflow: hidden;

    display: block;

    background: #151515;

    border: 3px solid #ff9f1c;

    box-shadow:
        0 0 0 3px rgba(255,159,28,0.10),
        0 0 22px rgba(255,159,28,0.18),
        0 8px 30px rgba(0,0,0,0.50);

    flex-shrink: 0;
    box-sizing: border-box;
}


/* IMAGE MUST FILL THE ENTIRE CIRCLE */

.public-profile-image img {
    width: 100%;
    height: 100%;

    min-width: 100%;
    min-height: 100%;

    max-width: none;
    max-height: none;

    margin: 0;
    padding: 0;

    display: block;

    object-fit: cover;
    object-position: center;

    border: none;
    border-radius: 50%;

    box-sizing: border-box;
}


/* =========================
   PROFILE INFORMATION
========================= */

.public-profile-info {
    min-width: 0;

    position: relative;

    z-index: 1;
}


.public-profile-info h1 {
    margin: 0 0 5px;

    color: #fff;

    font-size: 30px;

    font-weight: 700;

    letter-spacing: -0.3px;
}


.public-profile-username {
    margin: 0 0 12px;

    color: #ff9f1c;

    font-size: 13px;

    font-weight: 500;
}


.public-profile-bio {
    margin: 0;

    color: #ccc;

    line-height: 1.6;

    max-width: 680px;

    font-size: 14px;
}


/* =========================
   PROFILE STATS
========================= */

.public-profile-stats {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 1px;

    margin-top: 25px;

    background:
        rgba(255,159,28,0.16);

    border:
        1px solid rgba(255,159,28,0.18);

    border-radius: 10px;

    overflow: hidden;
}


.public-profile-stat {
    background:
        rgba(255,255,255,0.035);

    padding: 18px;

    text-align: center;

    transition:
        background 0.2s ease;
}


.public-profile-stat:hover {
    background:
        rgba(255,159,28,0.08);
}


.public-profile-stat strong {
    display: block;

    color: #ff9f1c;

    font-size: 22px;

    font-weight: 700;

    margin-bottom: 4px;
}


.public-profile-stat span {
    color: #999;

    font-size: 11px;

    text-transform: uppercase;

    letter-spacing: 0.8px;
}


/* =========================
   SECTIONS
========================= */

.public-profile-section {
    position: relative;

    border-top:
        1px solid rgba(255,255,255,0.08);

    padding-top: 25px;

    margin-top: 30px;
}


.public-profile-section h2 {
    margin: 0 0 18px;

    color: #fff;

    font-size: 18px;

    font-weight: 600;

    letter-spacing: -0.1px;
}


/* =========================
   ORANGE SECTION MARKER
========================= */

.public-profile-section h2::before {
    content: "";

    display: inline-block;

    width: 4px;

    height: 17px;

    margin-right: 9px;

    vertical-align: -2px;

    background:
        #ff9f1c;

    border-radius: 3px;

    box-shadow:
        0 0 8px rgba(255,159,28,0.35);
}


/* =========================
   MOVIE STATS
========================= */

.public-movie-stats {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 12px;
}


.public-movie-stat {
    background:
        linear-gradient(
            135deg,
            rgba(255,159,28,0.08),
            rgba(255,255,255,0.035)
        );

    border:
        1px solid rgba(255,159,28,0.15);

    border-radius: 9px;

    padding: 16px;

    border-left:
        3px solid #ff9f1c;

    transition:
        background 0.2s ease,
        transform 0.2s ease,
        border-color 0.2s ease;
}


.public-movie-stat:hover {
    background:
        linear-gradient(
            135deg,
            rgba(255,159,28,0.13),
            rgba(255,255,255,0.05)
        );

    transform:
        translateY(-2px);

    border-color:
        rgba(255,159,28,0.35);
}


.public-movie-stat:nth-child(2) {
    border-left-color:
        #ffb52e;
}


.public-movie-stat:nth-child(3) {
    border-left-color:
        #ffcc70;
}


.public-movie-stat span {
    display: block;

    color: #888;

    font-size: 11px;

    margin-bottom: 7px;

    text-transform: uppercase;

    letter-spacing: 0.6px;
}


.public-movie-stat strong {
    color: #fff;

    font-size: 20px;
}


/* =========================
   FAVORITE GENRES
========================= */

.public-genre-list {
    display: flex;

    flex-wrap: wrap;

    gap: 8px;
}


.public-genre-list span {
    padding: 7px 12px;

    border-radius: 5px;

    background:
        rgba(255,159,28,0.08);

    border:
        1px solid rgba(255,159,28,0.22);

    color: #ffcf91;

    font-size: 12px;

    transition:
        background 0.2s ease,
        border-color 0.2s ease,
        color 0.2s ease;
}


.public-genre-list span:hover {
    background:
        rgba(255,159,28,0.16);

    border-color:
        rgba(255,159,28,0.45);

    color: #fff;
}


/* =========================
   TOP 5 MOVIES
========================= */

.public-top-movies {
    display: grid;

    grid-template-columns:
        repeat(5, 1fr);

    gap: 14px;

    margin: 0;

    padding: 0;

    list-style: none;
}


.public-top-movies > div {
    position: relative;

    background:
        #181818;

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 8px;

    overflow: hidden;

    transition:
        transform 0.25s ease,
        border-color 0.25s ease,
        box-shadow 0.25s ease;
}


.public-top-movies > div:hover {
    transform:
        translateY(-5px);

    border-color:
        rgba(255,159,28,0.55);

    box-shadow:
        0 8px 25px rgba(0,0,0,0.35),
        0 0 15px rgba(255,159,28,0.08);
}


.public-top-movies img {
    width: 100%;

    height: 210px;

    object-fit: cover;

    display: block;

    background:
        #222;
}


.public-top-movie-title {
    padding: 10px;

    color: #eee;

    font-size: 12px;

    line-height: 1.4;

    min-height: 48px;

    box-sizing: border-box;
}


/* =========================
   WATCHED MOVIES
========================= */

.watched-movies-grid {
    display: grid;

    grid-template-columns:
        repeat(5, 1fr);

    gap: 14px;
}


.watched-movie-card {
    background:
        #181818;

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 8px;

    overflow: hidden;

    transition:
        transform 0.25s ease,
        border-color 0.25s ease,
        background 0.25s ease,
        box-shadow 0.25s ease;
}


.watched-movie-card:hover {
    transform:
        translateY(-5px);

    border-color:
        rgba(255,159,28,0.55);

    background:
        #1d1d1d;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.35),
        0 0 15px rgba(255,159,28,0.08);
}


.watched-movie-card img {
    width: 100%;

    height: 210px;

    object-fit: cover;

    display: block;

    background:
        #222;
}


.watched-movie-info {
    padding: 11px;
}


.watched-movie-info h3 {
    margin: 0 0 6px;

    color: #eee;

    font-size: 12px;

    font-weight: 600;

    line-height: 1.4;
}


.watched-movie-info p {
    margin: 0;

    color: #777;

    font-size: 10px;
}


.no-watched {
    color: #777;

    font-size: 13px;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

    .public-top-movies,
    .watched-movies-grid {
        grid-template-columns:
            repeat(4, 1fr);
    }

}


@media (max-width: 700px) {

    .public-profile-card {
        padding: 22px;
    }


    .public-profile-top {
        gap: 18px;
    }


    .public-profile-image {
        width: 95px;

        height: 95px;

        min-width: 95px;

        min-height: 95px;
    }


    .public-profile-info h1 {
        font-size: 25px;
    }


    .public-top-movies,
    .watched-movies-grid {
        grid-template-columns:
            repeat(3, 1fr);
    }

}


@media (max-width: 550px) {

    .public-profile-container {
        padding: 0 14px;
    }


    .public-profile-top {
        flex-direction: column;

        text-align: center;
    }


    .public-profile-bio {
        max-width: 100%;
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

                <?php if ($profilePicture !== ""): ?>

                    <img
                        class="profile-picture"
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

                    if (!empty($profileUser["bio"])) {

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

                <?php if (!empty($favoriteGenres)): ?>

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
                                            $watchedMovie["poster"]
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $watchedMovie["title"]
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
                                        $watchedMovie["title"]
                                    );

                                    ?>

                                </h3>


                                <?php if (
                                    !empty(
                                        $watchedMovie["watchedDate"]
                                    )
                                ): ?>

                                    <p>

                                        Watched:

                                        <?php

                                        echo date(
                                            "M d, Y",
                                            strtotime(
                                                $watchedMovie["watchedDate"]
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