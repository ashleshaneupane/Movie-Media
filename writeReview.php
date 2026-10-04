<?php

include 'includes/auth.php';
include 'includes/config.php';


/* =========================
   CHECK MOVIE ID
========================= */

if (!isset($_GET['movie']) || !is_numeric($_GET['movie'])) {

    die("Invalid movie.");

}

$movieID = (int) $_GET['movie'];


/* =========================
   GET MOVIE
========================= */

$movieQuery = $conn->prepare(
    "SELECT *
     FROM movie
     WHERE movieID = ?"
);

$movieQuery->bind_param("i", $movieID);

$movieQuery->execute();

$movieResult = $movieQuery->get_result();


if ($movieResult->num_rows === 0) {

    die("Movie not found.");

}

$movie = $movieResult->fetch_assoc();


/* =========================
   CURRENT USER
========================= */
$userID = $_SESSION["userID"];

/* Check if user has watched this movie */
$watchedQuery = $conn->prepare(
    "SELECT watchedID
     FROM Watched
     WHERE userID = ? AND movieID = ?"
);

$watchedQuery->bind_param("ii", $userID, $movieID);
$watchedQuery->execute();

$watchedResult = $watchedQuery->get_result();

$isWatched = $watchedResult->num_rows > 0;

if (!$isWatched) {
    die("You must watch this movie before writing a review.");
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* Get rating */

$rating = isset($_POST["rating"])
    ? (float) $_POST["rating"]
    : 0;



    /* Get review text */

    $reviewText = isset($_POST["reviewText"])
        ? trim($_POST["reviewText"])
        : "";


    /* Get spoiler */

    $spoiler = isset($_POST["spoiler"])
        ? 1
        : 0;


    /* =========================
       VALIDATE RATING
    ========================= */

   if ($rating < 0.5 || $rating > 10 || fmod($rating, 0.5) != 0) {
    $error = "Please select a valid rating.";
}


    /* =========================
       VALIDATE REVIEW TEXT
    ========================= */

    elseif ($reviewText === "") {

        $error = "Please write a review.";

    }


    /* =========================
       CHECK EXISTING REVIEW
    ========================= */

    else {

        $checkReview = $conn->prepare(
            "SELECT reviewID
             FROM Review
             WHERE userID = ? AND movieID = ?"
        );

        $checkReview->bind_param(
            "ii",
            $userID,
            $movieID
        );

        $checkReview->execute();

        $checkResult = $checkReview->get_result();


        if ($checkResult->num_rows > 0) {

            $error = "You have already reviewed this movie.";

        }


        /* =========================
           INSERT REVIEW
        ========================= */

        else {

            $insertReview = $conn->prepare(
                "INSERT INTO Review
                (
                    userID,
                    movieID,
                    rating,
                    reviewText,
                    spoiler
                )
                VALUES (?, ?, ?, ?, ?)"
            );

            $insertReview->bind_param(
                "iidsi",
                $userID,
                $movieID,
                $rating,
                $reviewText,
                $spoiler
            );


            if ($insertReview->execute()) {

                header(
                    "Location: movieDetails.php?movie="
                    . $movieID
                    . "&review=success"
                );

                exit;

            }

            else {

                $error = "Something went wrong while posting your review.";

            }

        }

    }

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

    <title>
        Write Review - <?php echo htmlspecialchars($movie['title']); ?>
    </title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>


<?php include 'includes/header.php'; ?>


<main class="write-review-container">


    <a
        href="javascript:history.back()"
        class="back-button"
    >
        ←
    </a>


    <section class="write-review-card">


        <h1>
            Write a Review
        </h1>


        <!-- MOVIE INFORMATION -->

        <div class="review-movie">


            <div class="review-movie-poster">

                <img
                    src="<?php echo htmlspecialchars($movie['poster']); ?>"
                    alt="<?php echo htmlspecialchars($movie['title']); ?>"
                >

            </div>


            <div class="review-movie-info">

                <h2>
                    <?php echo htmlspecialchars($movie['title']); ?>
                </h2>


                <p>

                    <?php echo htmlspecialchars($movie['genre']); ?>

                    •

                    <?php echo htmlspecialchars($movie['releaseDate']); ?>

                </p>

            </div>


        </div>


        <!-- ERROR MESSAGE -->

        <?php if ($error !== ""): ?>

            <p class="review-error">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>


        <!-- REVIEW FORM -->

        <form
            id="writeReviewForm"
            method="POST"
        >


            <!-- RATING -->

            <div class="review-field">


                <label>
                    Your Rating
                </label>


                <div class="rating-container">


                    <button
                        type="button"
                        class="rating-star"
                        data-value="1"
                    >
                        ☆
                    </button>


                    <button
                        type="button"
                        class="rating-star"
                        data-value="2"
                    >
                        ☆
                    </button>


                    <button
                        type="button"
                        class="rating-star"
                        data-value="3"
                    >
                        ☆
                    </button>


                    <button
                        type="button"
                        class="rating-star"
                        data-value="4"
                    >
                        ☆
                    </button>


                    <button
                        type="button"
                        class="rating-star"
                        data-value="5"
                    >
                        ☆
                    </button>


                </div>


                <input
                    type="hidden"
                    id="rating"
                    name="rating"
                    value=""
                >


                <p
                    id="ratingValue"
                    class="rating-value"
                >
                    No rating selected
                </p>


            </div>


            <!-- REVIEW TEXT -->

            <div class="review-field">


                <label for="reviewText">
                    Your Review
                </label>


                <textarea
                    id="reviewText"
                    name="reviewText"
                    placeholder="What did you think about this movie?"
                    required
                ></textarea>


            </div>


            <!-- SPOILER -->

            <div class="review-field">


                <label class="spoiler-option">


                    <input
                        type="checkbox"
                        id="spoiler"
                        name="spoiler"
                    >


                    Contains spoilers


                </label>


            </div>


            <!-- SUBMIT -->

            <div class="review-submit">


                <button type="submit">
                    Post Review
                </button>


            </div>


        </form>


    </section>


</main>


<script>

/* =========================
   STAR RATING
========================= */

const stars =
    document.querySelectorAll(".rating-star");

const ratingInput =
    document.getElementById("rating");

const ratingValue =
    document.getElementById("ratingValue");


let selectedStar = 0;

let selectedState = 0;

/*
    0 = empty
    1 = full
    2 = half
*/


stars.forEach(function(star) {

    star.addEventListener(
        "click",
        function() {

            const clickedStar =
                Number(this.dataset.value);


            /* Different star */

            if (clickedStar !== selectedStar) {

                selectedStar =
                    clickedStar;

                selectedState =
                    1;

            }


            /* Same star */

            else {

                if (selectedState === 1) {

                    selectedState =
                        2;

                }

                else if (selectedState === 2) {

                    selectedStar =
                        0;

                    selectedState =
                        0;

                }

            }


            updateStars();

        }
    );

});


function updateStars() {

    stars.forEach(function(star) {

        const value =
            Number(star.dataset.value);


        /* Full stars */

        if (
            selectedStar > 0 &&
            value < selectedStar
        ) {

            star.textContent =
                "★";

        }


        /* Selected full star */

        else if (
            value === selectedStar &&
            selectedState === 1
        ) {

            star.textContent =
                "★";

        }


        /* Selected half star */

        else if (
            value === selectedStar &&
            selectedState === 2
        ) {

            star.textContent =
                "⯨";

        }


        /* Empty */

        else {

            star.textContent =
                "☆";

        }

    });


    /* =========================
       CALCULATE RATING OUT OF 10
    ========================= */

    let rating = 0;


    if (selectedState === 1) {

        rating =
            selectedStar * 2;

    }


    else if (selectedState === 2) {

        rating =
            (selectedStar * 2) - 1;

    }


    ratingInput.value =
        rating;


    /* =========================
       DISPLAY RATING
    ========================= */

    if (rating === 0) {

        ratingValue.textContent =
            "No rating selected";

    }

    else {

        ratingValue.textContent =
            rating + " / 10";

    }

}

</script>


</body>

</html>