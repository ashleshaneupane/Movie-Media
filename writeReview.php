<?php
include 'includes/auth.php';
include 'includes/config.php';

// Check movie ID
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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Write Review - <?php echo htmlspecialchars($movie['title']); ?>
    </title>

    <link rel="stylesheet" href="css/index.css">

</head>

<body>

<?php include 'includes/header.php'; ?>


<main class="write-review-container">

    <a href="javascript:history.back()" class="back-button">←</a>


    <section class="write-review-card">

        <h1>Write a Review</h1>


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


        <!-- REVIEW FORM -->

        <form id="writeReviewForm">


            <!-- RATING -->

            <div class="review-field">

                <label>Your Rating</label>


                <div class="rating-container">

                    <button type="button" class="rating-star" data-value="1">
                        ☆
                    </button>

                    <button type="button" class="rating-star" data-value="2">
                        ☆
                    </button>

                    <button type="button" class="rating-star" data-value="3">
                        ☆
                    </button>

                    <button type="button" class="rating-star" data-value="4">
                        ☆
                    </button>

                    <button type="button" class="rating-star" data-value="5">
                        ☆
                    </button>

                </div>


                <input
                    type="hidden"
                    id="rating"
                    name="rating"
                    value=""
                >


                <p id="ratingValue" class="rating-value">
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

const stars =
    document.querySelectorAll(".rating-star");

const ratingInput =
    document.getElementById("rating");

const ratingValue =
    document.getElementById("ratingValue");


let selectedStar = 0;

let selectedState = 0;
// 0 = empty
// 1 = full
// 2 = half


stars.forEach(function(star) {

    star.addEventListener("click", function() {

        const clickedStar =
            Number(this.dataset.value);


        // If a different star is clicked
        if (clickedStar !== selectedStar) {

            selectedStar = clickedStar;

            selectedState = 1;

        }

        // Same star clicked again
        else {

            if (selectedState === 1) {

                selectedState = 2;

            }

            else if (selectedState === 2) {

                selectedStar = 0;

                selectedState = 0;

            }

        }


        updateStars();

    });

});


function updateStars() {

    stars.forEach(function(star) {

        const value =
            Number(star.dataset.value);


        // Full stars before selected star
        if (
            selectedStar > 0 &&
            value < selectedStar
        ) {

            star.textContent = "★";

        }


        // Selected star
        else if (
            value === selectedStar &&
            selectedState === 1
        ) {

            star.textContent = "★";

        }


        // Selected star as half
        else if (
            value === selectedStar &&
            selectedState === 2
        ) {

            star.textContent = "⯨";

        }


        // Everything after selected star
        else {

            star.textContent = "☆";

        }

    });


    // Calculate rating

    let rating = 0;


    if (selectedState === 1) {

        rating = selectedStar;

    }

    else if (selectedState === 2) {

        rating = selectedStar - 0.5;

    }


    ratingInput.value = rating;


    if (rating === 0) {

        ratingValue.textContent =
            "No rating selected";

    }

    else {

        ratingValue.textContent =
            rating + " / 5";

    }

}


const writeReviewForm =
    document.getElementById("writeReviewForm");


writeReviewForm.addEventListener(
    "submit",
    function(event) {

        event.preventDefault();


        const rating =
            Number(ratingInput.value);


        const reviewText =
            document.getElementById(
                "reviewText"
            ).value.trim();


        if (rating === 0) {

            alert("Please select a rating.");

            return;

        }


        if (reviewText === "") {

            alert("Please write a review.");

            return;

        }


        alert(
            "Your review has been posted successfully!"
        );


        window.location.href =
            "movieDetails.php?movie=<?php echo $movieID; ?>";

    }
);

</script>


</body>

</html>