<?php

include 'includes/auth.php';
include 'includes/config.php';


// Get movie ID from URL

if (!isset($_GET['movie']) || !is_numeric($_GET['movie'])) {

    die("Invalid movie.");

}

$movieID = (int) $_GET['movie'];


// Get movie from database

$sql = "SELECT * FROM movie WHERE movieID = $movieID";

$result = $conn->query($sql);


if ($result->num_rows === 0) {

    die("Movie not found.");

}

$movie = $result->fetch_assoc();

$userID = $_SESSION["userID"];


/*
    Check if movie is already in Watchlist
*/

$watchlistQuery = $conn->prepare(
    "SELECT watchlistID
     FROM Watchlist
     WHERE userID = ? AND movieID = ?"
);

$watchlistQuery->bind_param("ii", $userID, $movieID);
$watchlistQuery->execute();

$watchlistResult = $watchlistQuery->get_result();

$isInWatchlist = $watchlistResult->num_rows > 0;


/*
    Check if movie is already Watched
*/

$watchedQuery = $conn->prepare(
    "SELECT watchedID
     FROM Watched
     WHERE userID = ? AND movieID = ?"
);

$watchedQuery->bind_param("ii", $userID, $movieID);
$watchedQuery->execute();

$watchedResult = $watchedQuery->get_result();

$isWatched = $watchedResult->num_rows > 0;


/*
    Get reviews for this movie
*/

$reviewQuery = $conn->prepare(
    "SELECT
        Review.reviewID,
        Review.rating,
        Review.reviewText,
        Review.spoiler,
        Review.reviewDate,
        Users.username,
        Users.profilePicture
     FROM Review
     INNER JOIN Users
        ON Review.userID = Users.userID
     WHERE Review.movieID = ?
     ORDER BY Review.reviewDate DESC"
);

$reviewQuery->bind_param("i", $movieID);

$reviewQuery->execute();

$reviewResult = $reviewQuery->get_result();
/*
    Get movie average rating
*/

$ratingQuery = $conn->prepare(
    "SELECT AVG(rating) AS averageRating
     FROM Review
     WHERE movieID = ?"
);

$ratingQuery->bind_param("i", $movieID);
$ratingQuery->execute();

$ratingResult = $ratingQuery->get_result();
$ratingData = $ratingResult->fetch_assoc();

$averageRating = $ratingData["averageRating"];

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
        <?php echo htmlspecialchars($movie['title']); ?>
        - Movie Media
    </title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="movie-details-container">


    <!-- BACK -->

    <a
        href="search.php"
        class="back-button"
    >
        ←
    </a>


    <!-- MOVIE HEADER -->

    <section class="movie-details-header">


        <div class="details-poster">

            <img
                src="<?php echo htmlspecialchars($movie['poster']); ?>"
                alt="<?php echo htmlspecialchars($movie['title']); ?>"
            >

        </div>


        <div class="movie-main-info">


            <h1>

                <?php echo htmlspecialchars($movie['title']); ?>

            </h1>


            <p class="movie-meta">

                <?php echo htmlspecialchars($movie['releaseDate']); ?>

                •

                <?php echo htmlspecialchars($movie['genre']); ?>

            </p>


        <div class="movie-rating">

    <?php if ($averageRating === null): ?>

        ⭐ No rating yet

    <?php else: ?>

        ⭐ <?php echo number_format($averageRating, 1); ?>

        <span>
            / 10
        </span>

    <?php endif; ?>

</div>


            <p class="movie-overview">

                <?php echo htmlspecialchars($movie['description']); ?>

            </p>


            <div class="movie-actions">


                <!-- WATCHLIST BUTTON -->

<button
    type="button"
    id="watchlistBtn"
    data-movie-id="<?php echo $movie['movieID']; ?>"
    data-watched="<?php echo $isWatched ? 'true' : 'false'; ?>"
    <?php echo $isInWatchlist ? "disabled" : ""; ?>
>

    <?php

    if ($isInWatchlist) {
        echo "✓ In Watchlist";
    }
    else {
        echo "+ Watchlist";
    }

    ?>

</button>


                <!-- WATCHED BUTTON -->

                <button
                    type="button"
                    id="watchedBtn"
                    data-movie-id="<?php echo $movie['movieID']; ?>"
                    <?php echo $isWatched ? "disabled" : ""; ?>
                >

                    <?php

                    if ($isWatched) {

                        echo "✓ Already Watched";

                    }
                    else {

                        echo "✓ Watched";

                    }

                    ?>

                </button>


<!-- REVIEW -->

<?php if ($isWatched): ?>

    <a
        href="writeReview.php?movie=<?php echo $movie['movieID']; ?>"
        class="review-button"
        id="reviewBtn"
    >
        Review
    </a>

<?php else: ?>

    <button
        type="button"
        class="review-button review-disabled"
        id="reviewBtn"
        onclick="alert('You must watch this movie before you can review it.')"
    >
        Review
    </button>

<?php endif; ?>


            </div>


        </div>


    </section>


    <!-- MOVIE INFORMATION -->

    <section class="movie-information">


        <div>


            <h2>
                Details
            </h2>


            <p>

                <strong>
                    Release:
                </strong>

                <?php echo htmlspecialchars($movie['releaseDate']); ?>

            </p>


            <p>

                <strong>
                    Genre:
                </strong>

                <?php echo htmlspecialchars($movie['genre']); ?>

            </p>


            <p>

                <strong>
                    Language:
                </strong>

                <?php echo htmlspecialchars($movie['language']); ?>

            </p>


            <p>

                <strong>
                    Runtime:
                </strong>

                <?php echo htmlspecialchars($movie['runtime']); ?>

                minutes

            </p>


        </div>


    </section>


    <!-- REVIEWS -->

<section class="movie-reviews">


    <div class="section-heading">

        <h2>
            Reviews
        </h2>

    </div>


    <?php if ($reviewResult->num_rows === 0): ?>


        <p class="no-reviews">
            No reviews yet.
        </p>


    <?php else: ?>


        <div class="movie-review-list">


            <?php while ($review = $reviewResult->fetch_assoc()): ?>


                <article class="movie-review-card">


                    <!-- USER -->

                    <div class="review-user">


                        <?php if (!empty($review["profilePicture"])): ?>

                            <img
                                src="<?php echo htmlspecialchars($review["profilePicture"]); ?>"
                                alt="<?php echo htmlspecialchars($review["username"]); ?>"
                                class="review-user-picture"
                            >

                        <?php endif; ?>


                        <div>

                            <h3>
                                <?php echo htmlspecialchars($review["username"]); ?>
                            </h3>


                            <p class="review-date">

                                <?php
                                echo date(
                                    "M d, Y",
                                    strtotime($review["reviewDate"])
                                );
                                ?>

                            </p>

                        </div>


                    </div>


                    <!-- RATING -->

                    <div class="movie-review-rating">


                      <?php

$rating = (float) $review["rating"];

$starRating = $rating / 2;

$fullStars = floor($starRating);

$hasHalfStar = ($starRating - $fullStars) == 0.5;

$emptyStars =
    5
    - $fullStars
    - ($hasHalfStar ? 1 : 0);


for ($i = 0; $i < $fullStars; $i++) {

    echo "★";

}

if ($hasHalfStar) {

    echo "⯨";

}

for ($i = 0; $i < $emptyStars; $i++) {

    echo "☆";

}

?>

<span>
    <?php echo htmlspecialchars($review["rating"]); ?>/10
</span>

                    </div>


                    <!-- REVIEW TEXT -->

                  <p
    class="movie-review-text <?php echo $review["spoiler"] ? "spoiler-blurred" : ""; ?>"
    <?php echo $review["spoiler"] ? 'onclick="this.classList.toggle(\'spoiler-revealed\')"' : ''; ?>
>

    <?php echo htmlspecialchars($review["reviewText"]); ?>

</p>


                    <!-- SPOILER -->

                    <?php if ($review["spoiler"]): ?>

                        <p class="review-spoiler">
                            ⚠ Contains spoilers
                        </p>

                    <?php endif; ?>


                </article>


            <?php endwhile; ?>


        </div>


    <?php endif; ?>


</section>


</main>


<script>


const watchlistBtn =
    document.getElementById("watchlistBtn");


const watchedBtn =
    document.getElementById("watchedBtn");


/*
    ADD TO WATCHLIST
*/

watchlistBtn.addEventListener("click", function() {

    if (watchlistBtn.dataset.watched === "true") {

        alert("You can't add a movie to your watchlist because you already watched it.");

        return;
    }

    const movieID =
        watchlistBtn.dataset.movieId;


    const formData =
        new FormData();


    formData.append(
        "movieID",
        movieID
    );


    fetch("addWatchlist.php", {

        method: "POST",

        body: formData

    })


    .then(function(response) {

        return response.text();

    })


    .then(function(result) {


        console.log(
            "Watchlist result:",
            result
        );


        if (result === "success") {


            watchlistBtn.textContent =
                "✓ In Watchlist";


            watchlistBtn.disabled =
                true;


        }


        else if (result === "exists") {


            watchlistBtn.textContent =
                "✓ Already in Watchlist";


            watchlistBtn.disabled =
                true;


        }


     else if (result === "watched") {

    watchlistBtn.textContent =
        "+ Watchlist";

    watchlistBtn.disabled =
        true;

}


        else {


            console.log(
                "Watchlist error:",
                result
            );

        }


    })


    .catch(function(error) {


        console.log(
            "Error:",
            error
        );


    });


});


/*
    MARK AS WATCHED
*/

watchedBtn.addEventListener("click", function() {


    const movieID =
        watchedBtn.dataset.movieId;


    const formData =
        new FormData();


    formData.append(
        "movieID",
        movieID
    );


    fetch("markWatched.php", {

        method: "POST",

        body: formData

    })


    .then(function(response) {

        return response.text();

    })


    .then(function(result) {


        console.log(
            "Watched result:",
            result
        );


        if (result === "success") {

    watchedBtn.textContent =
        "✓ Already Watched";

    watchedBtn.disabled =
        true;


    watchlistBtn.textContent =
        "+ Watchlist";

    watchlistBtn.disabled =
        true;


    reviewBtn.outerHTML =
        '<a href="writeReview.php?movie=' +
        movieID +
        '" class="review-button" id="reviewBtn">Review</a>';

}


        else if (result === "exists") {


            watchedBtn.textContent =
                "✓ Already Watched";


            watchedBtn.disabled =
                true;


            watchlistBtn.textContent =
                "+ Watchlist";


            watchlistBtn.disabled =
                true;


        }


        else {


            console.log(
                "Watched error:",
                result
            );

        }


    })


    .catch(function(error) {


        console.log(
            "Error:",
            error
        );


    });


});

</script>


</body>

</html>