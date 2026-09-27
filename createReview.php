<?php
include 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Review - Movie Media</title>

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



            <!-- MOVIE -->

            <div class="create-review-field">

                <label for="reviewMovie">
                    Movie
                </label>


                <div class="movie-input-wrapper">

                    <input
                        type="text"
                        id="reviewMovie"
                        placeholder="Search movie..."
                    >

                    <span>
                        🔎
                    </span>

                </div>

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
                        ★
                    </span>

                    <span data-rating="2">
                        ★
                    </span>

                    <span data-rating="3">
                        ★
                    </span>

                    <span data-rating="4">
                        ★
                    </span>

                    <span data-rating="5">
                        ★
                    </span>

                </div>


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
                    placeholder="Write your review..."
                ></textarea>

            </div>



            <!-- SPOILER -->

            <label class="spoiler-option">

                <input
                    type="checkbox"
                    id="spoiler"
                >

                <span>
                    Contains spoilers
                </span>

            </label>



            <!-- BUTTONS -->

            <div class="create-review-actions">

                <button
                    type="button"
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


        </div>


    </main>



    <script>

        const stars =
            document.querySelectorAll(
                "#starRating span"
            );


        const ratingValue =
            document.getElementById(
                "ratingValue"
            );


        let selectedRating = 0;



        stars.forEach(function(star) {

            star.addEventListener(
                "click",
                function() {

                    selectedRating =
                        Number(
                            star.dataset.rating
                        );


                    stars.forEach(
                        function(item) {

                            const itemRating =
                                Number(
                                    item.dataset.rating
                                );


                            if (
                                itemRating <=
                                selectedRating
                            ) {

                                item.classList.add(
                                    "selected"
                                );

                            } else {

                                item.classList.remove(
                                    "selected"
                                );

                            }

                        }
                    );


                    ratingValue.textContent =
                        selectedRating +
                        " / 5";

                }
            );

        });



        const postReviewBtn =
            document.getElementById(
                "postReviewBtn"
            );


        postReviewBtn.addEventListener(
            "click",
            function() {

                const movie =
                    document.getElementById(
                        "reviewMovie"
                    ).value.trim();


                const review =
                    document.getElementById(
                        "reviewText"
                    ).value.trim();


                if (movie === "") {

                    alert(
                        "Please select a movie."
                    );

                    return;

                }


                if (selectedRating === 0) {

                    alert(
                        "Please select a rating."
                    );

                    return;

                }


                if (review === "") {

                    alert(
                        "Please write a review."
                    );

                    return;

                }


                alert(
                    "Review posted successfully!"
                );


                window.location.href =
                    "review.php";

            }
        );

    </script>


</body>

</html>