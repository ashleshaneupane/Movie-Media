<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];

$error = "";


/* =========================
   GET MOVIES USER HAS WATCHED
========================= */

$movieQuery = $conn->prepare(
    "SELECT
        movie.movieID,
        movie.title,
        movie.poster
     FROM Watched
     INNER JOIN movie
        ON Watched.movieID = movie.movieID
     WHERE Watched.userID = ?
     ORDER BY movie.title ASC"
);

$movieQuery->bind_param("i", $userID);

$movieQuery->execute();

$movieResult = $movieQuery->get_result();


/* =========================
   POST REVIEW
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $movieID = isset($_POST["movieID"])
        ? (int) $_POST["movieID"]
        : 0;

    $rating = isset($_POST["rating"])
        ? (float) $_POST["rating"]
        : 0;

    $reviewText = isset($_POST["reviewText"])
        ? trim($_POST["reviewText"])
        : "";

    $spoiler = isset($_POST["spoiler"])
        ? 1
        : 0;


    /* =========================
       CHECK MOVIE
    ========================= */

    if ($movieID <= 0) {

        $error = "Please select a movie.";

    }


    /* =========================
       CHECK WATCHED
    ========================= */

    else {

        $watchedQuery = $conn->prepare(
            "SELECT watchedID
             FROM Watched
             WHERE userID = ? AND movieID = ?"
        );

        $watchedQuery->bind_param(
            "ii",
            $userID,
            $movieID
        );

        $watchedQuery->execute();

        $watchedResult =
            $watchedQuery->get_result();


        if ($watchedResult->num_rows === 0) {

            $error =
                "You must watch this movie before writing a review.";

        }

    }


    /* =========================
       VALIDATE RATING
    ========================= */

    if ($error === "") {

        if (
            $rating < 0.5 ||
            $rating > 10 ||
            fmod($rating, 0.5) != 0
        ) {

            $error =
                "Please select a valid rating.";

        }

    }


    /* =========================
       VALIDATE REVIEW
    ========================= */

    if ($error === "") {

        if ($reviewText === "") {

            $error =
                "Please write a review.";

        }

    }


    /* =========================
       CHECK EXISTING REVIEW
    ========================= */

    if ($error === "") {

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

        $checkResult =
            $checkReview->get_result();


        if ($checkResult->num_rows > 0) {

            $error =
                "You have already reviewed this movie.";

        }

    }


    /* =========================
       INSERT REVIEW
    ========================= */

    if ($error === "") {

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

            header("Location: review.php");

            exit;

        } else {

            $error =
                "Something went wrong while posting your review.";

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
        Create Review - Movie Media
    </title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="create-review-container">


    <!-- BACK -->

    <a
        href="review.php"
        class="back-button"
    >
        ←
    </a>


    <div class="create-review-card">


        <h1>
            Create Review
        </h1>


        <p class="create-review-subtitle">
            Share what you thought about the movie.
        </p>


        <!-- ERROR -->

        <?php if ($error !== ""): ?>

            <p class="review-error">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>


        <form
            method="POST"
            id="createReviewForm"
        >


            <!-- MOVIE -->

            <div class="create-review-field">

                <label for="reviewMovie">
                    Movie
                </label>


                <select
                    id="reviewMovie"
                    name="movieID"
                    required
                >

                    <option value="">
                        Select a movie...
                    </option>


                    <?php if ($movieResult->num_rows > 0): ?>

                        <?php while ($movie = $movieResult->fetch_assoc()): ?>

                            <option
                                value="<?php echo $movie["movieID"]; ?>"
                            >
                                <?php echo htmlspecialchars($movie["title"]); ?>
                            </option>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <option value="" disabled>
                            You have not watched any movies yet.
                        </option>

                    <?php endif; ?>

                </select>

            </div>


            <!-- RATING -->

            <div class="create-review-field">

                <label>
                    Rate
                </label>


                <div
                    class="star-rating"
                    id="starRating"
                >

                    <span data-rating="1">
                        ☆
                    </span>

                    <span data-rating="2">
                        ☆
                    </span>

                    <span data-rating="3">
                        ☆
                    </span>

                    <span data-rating="4">
                        ☆
                    </span>

                    <span data-rating="5">
                        ☆
                    </span>

                </div>


                <input
                    type="hidden"
                    name="rating"
                    id="rating"
                    value=""
                >


                <p
                    class="rating-value"
                    id="ratingValue"
                >
                    Select a rating
                </p>

            </div>


            <!-- REVIEW -->

            <div class="create-review-field">

                <label for="reviewText">
                    Review
                </label>


                <textarea
                    id="reviewText"
                    name="reviewText"
                    placeholder="Write your review..."
                    required
                ></textarea>

            </div>


            <!-- SPOILER -->

            <label class="spoiler-option">

                <input
                    type="checkbox"
                    id="spoiler"
                    name="spoiler"
                >

                <span>
                    Contains spoilers
                </span>

            </label>


            <!-- BUTTONS -->

            <div class="create-review-actions">

                <button
                    type="submit"
                    class="post-review-btn"
                    id="postReviewBtn"
                >
                    POST
                </button>


                <a
                    href="review.php"
                    class="cancel-review-btn"
                >
                    Cancel
                </a>

            </div>


        </form>


    </div>


</main>


<script>

/* =========================
   STAR RATING
========================= */

const stars =
    document.querySelectorAll(
        "#starRating span"
    );


const ratingInput =
    document.getElementById(
        "rating"
    );


const ratingValue =
    document.getElementById(
        "ratingValue"
    );


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
                Number(
                    this.dataset.rating
                );


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
            Number(star.dataset.rating);

        star.classList.remove("selected");

        if (
            selectedStar > 0 &&
            value < selectedStar
        ) {

            star.textContent = "★";
            star.classList.add("selected");

        }

        else if (
            value === selectedStar &&
            selectedState === 1
        ) {

            star.textContent = "★";
            star.classList.add("selected");

        }

        else if (
            value === selectedStar &&
            selectedState === 2
        ) {

            star.textContent = "⯨";
            star.classList.add("selected");

        }

        else {

            star.textContent = "☆";

        }

    });


    let rating = 0;


    if (selectedState === 1) {

        rating = selectedStar * 2;

    }

    else if (selectedState === 2) {

        rating = (selectedStar * 2) - 1;

    }


    ratingInput.value = rating;


    if (rating === 0) {

        ratingValue.textContent =
            "Select a rating";

    }

    else {

        ratingValue.textContent =
            rating + " / 10";

    }

}
</script>


</body>

</html>