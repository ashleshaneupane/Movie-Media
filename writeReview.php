<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Write Review - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">
</head>

<body>

<?php include 'includes/header.php'; ?>

<main class="write-review-container">

    <a href="javascript:history.back()" class="back-button">←</a>

    <section class="write-review-card">

        <h1>Write a Review</h1>

        <div class="review-movie">

            <div class="review-movie-poster">
                INTERSTELLAR
            </div>

            <div class="review-movie-info">
                <h2>Interstellar</h2>
                <p>Sci-Fi • 2014</p>
            </div>

        </div>


        <form id="writeReviewForm">

            <!-- Rating -->

            <div class="review-field">

                <label>Your Rating</label>

                <div class="rating-container">

                    <input type="radio" name="rating" id="star5" value="5">
                    <label for="star5">★</label>

                    <input type="radio" name="rating" id="star4" value="4">
                    <label for="star4">★</label>

                    <input type="radio" name="rating" id="star3" value="3">
                    <label for="star3">★</label>

                    <input type="radio" name="rating" id="star2" value="2">
                    <label for="star2">★</label>

                    <input type="radio" name="rating" id="star1" value="1">
                    <label for="star1">★</label>

                </div>

            </div>


            <!-- Review -->

            <div class="review-field">

                <label for="reviewText">Your Review</label>

                <textarea
                    id="reviewText"
                    name="reviewText"
                    placeholder="What did you think about this movie?"
                    required
                ></textarea>

            </div>


            <!-- Submit -->

            <div class="review-submit">

                <button type="submit">
                    Post Review
                </button>

            </div>

        </form>

    </section>

</main>


<script>

const writeReviewForm = document.getElementById("writeReviewForm");

writeReviewForm.addEventListener("submit", function(event) {

    event.preventDefault();

    const rating = document.querySelector(
        'input[name="rating"]:checked'
    );

    const reviewText = document.getElementById("reviewText").value.trim();


    if (!rating) {

        alert("Please select a rating.");

        return;
    }


    if (reviewText === "") {

        alert("Please write a review.");

        return;
    }


    alert("Your review has been posted successfully!");

    window.location.href = "movieDetails.php?movie=interstellar";

});

</script>

</body>

</html>