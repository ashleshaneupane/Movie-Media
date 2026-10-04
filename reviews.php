<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/* GET USER'S REVIEWS */

$reviewQuery = $conn->prepare(
    "SELECT
        Review.reviewID,
        Review.movieID,
        Review.rating,
        Review.reviewText,
        Review.spoiler,
        Review.reviewDate,
        movie.title,
        movie.poster
     FROM Review
     INNER JOIN movie
        ON Review.movieID = movie.movieID
     WHERE Review.userID = ?
     ORDER BY Review.reviewDate DESC"
);

$reviewQuery->bind_param("i", $userID);
$reviewQuery->execute();

$reviewResult = $reviewQuery->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reviews - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="reviews-container">


        <!-- BACK BUTTON -->

        <a
            href="account.php"
            class="back-button"
        >
            ←
        </a>


        <!-- TITLE -->

        <h1 class="account-page-title">
            Reviews
        </h1>


        <!-- REVIEWS -->

        <section class="reviews-grid">


            <?php if ($reviewResult->num_rows === 0): ?>

              <p
    class="empty-reviews"
    style="color: white;"
>
    You haven't written any reviews yet.
</p>


            <?php else: ?>


                <?php while ($review = $reviewResult->fetch_assoc()): ?>


                    <article
                        class="review-card"
                        data-review-id="<?php echo $review["reviewID"]; ?>"
                    >


                        <!-- MOVIE POSTER -->

                        <div class="review-poster">

                            <img
                                src="<?php echo htmlspecialchars($review["poster"]); ?>"
                                alt="<?php echo htmlspecialchars($review["title"]); ?>"
                            >

                        </div>


                        <!-- MOVIE TITLE -->

                        <h2>
                            <?php echo htmlspecialchars($review["title"]); ?>
                        </h2>


                        <!-- RATING -->

                        <div class="rating">

                            <?php

                            $rating = (float) $review["rating"];

                            $fullStars = floor($rating);

                            $hasHalfStar = ($rating - $fullStars) == 0.5;

                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);


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

                        </div>


                        <!-- REVIEW TEXT -->

                        <p class="review-text">

                            <?php
                            echo htmlspecialchars($review["reviewText"]);
                            ?>

                        </p>


                        <!-- SPOILER -->

                        <?php if ($review["spoiler"]): ?>

                            <p class="review-spoiler">
                                ⚠ Contains spoilers
                            </p>

                        <?php endif; ?>


                        <!-- REVIEW DATE -->

                        <p class="review-date">

                            <?php
                            echo date(
                                "M d, Y",
                                strtotime($review["reviewDate"])
                            );
                            ?>

                        </p>


                        <!-- ACTIONS -->

                        <div class="review-actions">


                            <button
                                type="button"
                                class="review-edit-btn"
                                data-review-id="<?php echo $review["reviewID"]; ?>"
                            >
                                Edit
                            </button>


                            <button
                                type="button"
                                class="review-delete-btn"
                                data-review-id="<?php echo $review["reviewID"]; ?>"
                            >
                                Delete
                            </button>


                        </div>


                    </article>


                <?php endwhile; ?>


            <?php endif; ?>


        </section>


    </main>


    <script>


        /*
         * DELETE REVIEW
         */

        const deleteButtons =
            document.querySelectorAll(
                ".review-delete-btn"
            );


        deleteButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {


                    const reviewID =
                        button.dataset.reviewId;


                    const review =
                        button.closest(".review-card");


                    const confirmDelete =
                        confirm(
                            "Are you sure you want to delete this review?"
                        );


                    if (!confirmDelete) {
                        return;
                    }


                    const formData =
                        new FormData();


                    formData.append(
                        "reviewID",
                        reviewID
                    );


                    fetch("deleteReview.php", {

                        method: "POST",

                        body: formData

                    })

                    .then(function(response) {

                        return response.text();

                    })

                    .then(function(result) {


                        if (result === "success") {

                            review.remove();

                        }

                        else {

                            alert(
                                "Could not delete the review."
                            );

                            console.log(
                                "Delete review result:",
                                result
                            );

                        }

                    })

                    .catch(function(error) {

                        console.log(
                            "Delete review error:",
                            error
                        );

                    });


                }
            );

        });


        /*
         * EDIT REVIEW
         */

        const editButtons =
            document.querySelectorAll(
                ".review-edit-btn"
            );


        editButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {


                    const reviewID =
                        button.dataset.reviewId;


                   window.location.href =
    "editReview.php?review=" +
    reviewID;


                }
            );

        });


    </script>


</body>

</html>