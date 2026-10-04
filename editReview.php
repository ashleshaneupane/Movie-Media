<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/* =========================
   CHECK REVIEW ID
========================= */

if (
    !isset($_GET["review"]) ||
    !is_numeric($_GET["review"])
) {
    die("Invalid review.");
}

$reviewID = (int) $_GET["review"];


/* =========================
   GET REVIEW
========================= */

$reviewQuery = $conn->prepare(
    "SELECT
        Review.reviewID,
        Review.movieID,
        Review.rating,
        Review.reviewText,
        Review.spoiler,
        movie.title,
        movie.poster,
        movie.genre,
        movie.releaseDate
     FROM Review
     INNER JOIN movie
        ON Review.movieID = movie.movieID
     WHERE Review.reviewID = ?
     AND Review.userID = ?"
);

$reviewQuery->bind_param(
    "ii",
    $reviewID,
    $userID
);

$reviewQuery->execute();

$reviewResult = $reviewQuery->get_result();


if ($reviewResult->num_rows === 0) {
    die("Review not found.");
}


$review = $reviewResult->fetch_assoc();


$error = "";


/* =========================
   UPDATE REVIEW
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

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
       VALIDATE RATING
    ========================= */

    if (
        $rating < 0.5 ||
        $rating > 10 ||
        fmod($rating, 0.5) != 0
    ) {

        $error =
            "Please select a valid rating.";

    }


    /* =========================
       VALIDATE REVIEW
    ========================= */

    elseif ($reviewText === "") {

        $error =
            "Please write a review.";

    }


    /* =========================
       UPDATE DATABASE
    ========================= */

    else {

        $updateReview = $conn->prepare(
            "UPDATE Review
             SET rating = ?,
                 reviewText = ?,
                 spoiler = ?
             WHERE reviewID = ?
             AND userID = ?"
        );

        $updateReview->bind_param(
            "dsiii",
            $rating,
            $reviewText,
            $spoiler,
            $reviewID,
            $userID
        );


        if ($updateReview->execute()) {

            header(
                "Location: reviews.php?edit=success"
            );

            exit;

        } else {

            $error =
                "Something went wrong while updating your review.";

        }

    }

}


/* =========================
   CURRENT RATING
========================= */

$currentRating = (float) $review["rating"];

$fullStars =
    floor($currentRating / 2);

$hasHalfStar =
    ($currentRating % 2) == 1;

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
        Edit Review - Movie Media
    </title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="write-review-container">


    <!-- BACK -->

    <a
        href="reviews.php"
        class="back-button"
    >
        ←
    </a>


    <section class="write-review-card">


        <h1>
            Edit Review
        </h1>


        <!-- MOVIE INFORMATION -->

        <div class="review-movie">

            <div class="review-movie-poster">

                <img
                    src="<?php echo htmlspecialchars($review["poster"]); ?>"
                    alt="<?php echo htmlspecialchars($review["title"]); ?>"
                >

            </div>


            <div class="review-movie-info">

                <h2>
                    <?php echo htmlspecialchars($review["title"]); ?>
                </h2>


                <p>

                    <?php echo htmlspecialchars($review["genre"]); ?>

                    •

                    <?php echo htmlspecialchars($review["releaseDate"]); ?>

                </p>

            </div>

        </div>


        <!-- ERROR -->

        <?php if ($error !== ""): ?>

            <p class="review-error">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>


        <!-- FORM -->

        <form
            method="POST"
            id="editReviewForm"
        >


            <!-- RATING -->

            <div class="review-field">

                <label>
                    Your Rating
                </label>


                <div class="rating-container">

                    <?php for ($i = 1; $i <= 5; $i++): ?>

                        <button
                            type="button"
                            class="rating-star"
                            data-value="<?php echo $i; ?>"
                        >
                            <?php

                            $starValue = $i * 2;

                            if ($currentRating >= $starValue) {
                                echo "★";
                            }
                            elseif (
                                $currentRating == ($starValue - 1)
                            ) {
                                echo "⯨";
                            }
                            else {
                                echo "☆";
                            }

                            ?>
                        </button>

                    <?php endfor; ?>

                </div>


                <input
                    type="hidden"
                    id="rating"
                    name="rating"
                    value="<?php echo $currentRating; ?>"
                >


                <p
                    id="ratingValue"
                    class="rating-value"
                >
                    <?php echo $currentRating; ?> / 10
                </p>

            </div>


            <!-- REVIEW -->

            <div class="review-field">

                <label for="reviewText">
                    Your Review
                </label>


                <textarea
                    id="reviewText"
                    name="reviewText"
                    placeholder="What did you think about this movie?"
                    required
                ><?php echo htmlspecialchars($review["reviewText"]); ?></textarea>

            </div>


            <!-- SPOILER -->

            <div class="review-field">

                <label class="spoiler-option">

                    <input
                        type="checkbox"
                        id="spoiler"
                        name="spoiler"
                        <?php echo $review["spoiler"] ? "checked" : ""; ?>
                    >

                    Contains spoilers

                </label>

            </div>


            <!-- BUTTON -->

            <div class="review-submit">

                <button type="submit">
                    Update Review
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


let currentRating =
    Number(ratingInput.value);


/* =========================
   DISPLAY STARS
========================= */

function updateStars(rating) {

    stars.forEach(function(star) {

        const value =
            Number(star.dataset.value);

        const fullValue =
            value * 2;

        const halfValue =
            fullValue - 1;


        if (rating >= fullValue) {

            star.textContent = "★";

        }

        else if (rating === halfValue) {

            star.textContent = "⯨";

        }

        else {

            star.textContent = "☆";

        }

    });


    ratingValue.textContent =
        rating + " / 10";

}


/* =========================
   CLICK STAR
========================= */

stars.forEach(function(star) {

    star.addEventListener(
        "click",
        function() {

            const clickedStar =
                Number(this.dataset.value);


            if (
                currentRating ===
                clickedStar * 2
            ) {

                currentRating =
                    clickedStar * 2 - 1;

            }

            else if (
                currentRating ===
                clickedStar * 2 - 1
            ) {

                currentRating = 0;

            }

            else {

                currentRating =
                    clickedStar * 2;

            }


            ratingInput.value =
                currentRating;


            if (currentRating === 0) {

                ratingValue.textContent =
                    "No rating selected";

            }

            else {

                ratingValue.textContent =
                    currentRating + " / 10";

            }


            updateStars(currentRating);

        }
    );

});


/* =========================
   INITIAL DISPLAY
========================= */

updateStars(currentRating);

</script>


</body>

</html>