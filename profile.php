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
/* =========================================
   MOVIE MEDIA | PUBLIC PROFILE
========================================= */

.public-profile-container {
    width: 100%;
    max-width: 1180px;
    margin: 28px auto 60px;
    padding: 0 24px;
    box-sizing: border-box;
    color: #ededed;
}

/* BACK BUTTON */

.back-home-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    padding: 9px 13px;
    border: 1px solid #303030;
    border-radius: 7px;
    background: #191919;
    color: #bdbdbd;
    text-decoration: none;
    font-size: 13px;
    transition: background .2s, color .2s, border-color .2s;
}

.back-home-btn:hover {
    background: #242424;
    border-color: #555;
    color: #fff;
}

/* MAIN PROFILE CARD */

.public-profile-card {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    padding: 32px;
    box-sizing: border-box;
    background: #121212;
    border: 1px solid #292929;
    border-radius: 12px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, .22);
}

/* Subtle cinematic highlight */

.public-profile-card::before {
    content: "";
    position: absolute;
    z-index: -1;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(
        90deg,
        #d7a64a 0%,
        #a77b32 28%,
        #393027 65%,
        transparent 100%
    );
    pointer-events: none;
}

/* PROFILE HEADER */

.public-profile-top {
    position: relative;
    display: flex;
    align-items: center;
    gap: 25px;
    padding-bottom: 28px;
    border-bottom: 1px solid #292929;
}

/* PROFILE IMAGE */

.public-profile-image {
    width: 132px;
    height: 132px;
    min-width: 132px;
    min-height: 132px;
    flex-shrink: 0;
    display: block;
    overflow: hidden;
    box-sizing: border-box;
    border: 2px solid #39352e;
    border-radius: 50%;
    background: #202020;
}

.public-profile-image img {
    display: block;
    width: 100%;
    height: 100%;
    min-width: 100%;
    min-height: 100%;
    max-width: none;
    max-height: none;
    margin: 0;
    padding: 0;
    border: 0;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
    box-sizing: border-box;
}

/* PROFILE INFORMATION */

.public-profile-info {
    position: relative;
    z-index: 1;
    min-width: 0;
}

.public-profile-info h1 {
    margin: 0 0 7px;
    color: #f5f5f5;
    font-size: 29px;
    font-weight: 700;
    line-height: 1.25;
    letter-spacing: -.6px;
    overflow-wrap: anywhere;
}

.public-profile-username {
    margin: 0 0 13px;
    color: #c9a86a;
    font-size: 13px;
    font-weight: 500;
}

.public-profile-bio {
    max-width: 680px;
    margin: 0;
    color: #a9a9a9;
    font-size: 14px;
    line-height: 1.75;
    overflow-wrap: anywhere;
}

/* PROFILE STATS */

.public-profile-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1px;
    margin-top: 24px;
    overflow: hidden;
    border: 1px solid #2b2b2b;
    border-radius: 9px;
    background: #2b2b2b;
}

.public-profile-stat {
    min-width: 0;
    padding: 19px 12px;
    background: #191919;
    text-align: center;
    transition: background .2s ease;
}

.public-profile-stat:hover {
    background: #202020;
}

.public-profile-stat strong {
    display: block;
    margin-bottom: 6px;
    color: #e2bf7a;
    font-size: 23px;
    font-weight: 700;
    line-height: 1.2;
}

.public-profile-stat span {
    color: #8d8d8d;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* PROFILE SECTIONS */

.public-profile-section {
    position: relative;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid #292929;
}

.public-profile-section h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 19px;
    color: #f1f1f1;
    font-size: 18px;
    font-weight: 650;
    line-height: 1.4;
    letter-spacing: -.2px;
}

.public-profile-section h2::before {
    content: "";
    display: block;
    width: 3px;
    height: 19px;
    flex-shrink: 0;
    border-radius: 3px;
    background: #c9a15b;
}

/* MOVIE STATS */

.public-movie-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.public-movie-stat {
    min-width: 0;
    padding: 17px;
    border: 1px solid #2b2b2b;
    border-radius: 8px;
    border-left: 2px solid #77603a;
    background: #191919;
    transition: background .2s ease, border-color .2s ease;
}

.public-movie-stat:hover {
    background: #202020;
    border-color: #45403a;
}

.public-movie-stat:nth-child(2) {
    border-left-color: #88734f;
}

.public-movie-stat:nth-child(3) {
    border-left-color: #a18b67;
}

.public-movie-stat span {
    display: block;
    margin-bottom: 9px;
    color: #8c8c8c;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .9px;
}

.public-movie-stat strong {
    color: #e8e5df;
    font-size: 21px;
    font-weight: 650;
    overflow-wrap: anywhere;
}

/* FAVORITE GENRES */

.public-genre-list {
    display: flex;
    flex-wrap: wrap;
    gap: 9px;
}

.public-genre-list span {
    display: inline-flex;
    align-items: center;
    padding: 7px 12px;
    border: 1px solid #333;
    border-radius: 5px;
    background: #1b1b1b;
    color: #c9c9c9;
    font-size: 12px;
    transition: background .2s, border-color .2s, color .2s;
}

.public-genre-list span:hover {
    background: #25221c;
    border-color: #796442;
    color: #e4c58d;
}

/* TOP 5 MOVIES */

.public-top-movies {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.public-top-movies > div {
    min-width: 0;
    overflow: hidden;
    border: 1px solid #292929;
    border-radius: 8px;
    background: #191919;
    transition: transform .22s ease, border-color .22s ease;
}

.public-top-movies > div:hover {
    transform: translateY(-3px);
    border-color: #77603a;
}

.public-top-movies img {
    display: block;
    width: 100%;
    height: 220px;
    object-fit: cover;
    object-position: center;
    background: #222;
}

.public-top-movie-title {
    min-height: 48px;
    padding: 11px;
    box-sizing: border-box;
    color: #e5e5e5;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

/* WATCHED MOVIES */

.watched-movies-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
}

.watched-movie-card {
    min-width: 0;
    overflow: hidden;
    border: 1px solid #292929;
    border-radius: 8px;
    background: #191919;
    transition: transform .22s ease, border-color .22s ease;
}

.watched-movie-card:hover {
    transform: translateY(-3px);
    border-color: #77603a;
}

.watched-movie-card img {
    display: block;
    width: 100%;
    height: 220px;
    object-fit: cover;
    object-position: center;
    background: #222;
}

.watched-movie-info {
    padding: 12px;
}

.watched-movie-info h3 {
    margin: 0 0 7px;
    color: #e8e8e8;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.watched-movie-info p {
    margin: 0;
    color: #888;
    font-size: 11px;
    line-height: 1.5;
}

.no-watched {
    color: #888;
    font-size: 13px;
    line-height: 1.6;
}

/* =========================================
   RESPONSIVE DESIGN
========================================= */

@media (max-width: 900px) {
    .public-top-movies,
    .watched-movies-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .public-top-movies img,
    .watched-movie-card img {
        height: 200px;
    }
}

@media (max-width: 700px) {
    .public-profile-card {
        padding: 24px;
    }

    .public-profile-top {
        gap: 19px;
    }

    .public-profile-image {
        width: 105px;
        height: 105px;
        min-width: 105px;
        min-height: 105px;
    }

    .public-profile-info h1 {
        font-size: 25px;
    }

    .public-top-movies,
    .watched-movies-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .public-top-movies img,
    .watched-movie-card img {
        height: 190px;
    }
}

@media (max-width: 550px) {
    .public-profile-container {
        margin-top: 18px;
        padding: 0 13px;
    }

    .public-profile-card {
        padding: 19px;
        border-radius: 10px;
    }

    .public-profile-top {
        flex-direction: column;
        align-items: center;
        gap: 16px;
        text-align: center;
    }

    .public-profile-image {
        width: 112px;
        height: 112px;
        min-width: 112px;
        min-height: 112px;
    }

    .public-profile-info h1 {
        font-size: 24px;
    }

    .public-profile-bio {
        max-width: 100%;
        font-size: 13px;
    }

    .public-profile-stats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .public-profile-stat {
        padding: 15px 5px;
    }

    .public-profile-stat strong {
        font-size: 20px;
    }

    .public-profile-stat span {
        font-size: 9px;
        letter-spacing: .5px;
    }

    .public-movie-stats {
        grid-template-columns: 1fr;
    }

    .public-top-movies,
    .watched-movies-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 11px;
    }

    .public-top-movies img,
    .watched-movie-card img {
        height: 210px;
    }

    .public-profile-section {
        margin-top: 25px;
        padding-top: 21px;
    }

    .public-profile-section h2 {
        font-size: 17px;
    }
}

@media (max-width: 350px) {
    .public-profile-card {
        padding: 15px;
    }

    .public-top-movies img,
    .watched-movie-card img {
        height: 175px;
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