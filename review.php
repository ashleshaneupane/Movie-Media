<?php

include 'includes/auth.php';
include 'includes/config.php';


/*
    Get all reviews
*/

$sql = "
    SELECT
        Review.reviewID,
        Review.rating,
        Review.reviewText,
        Review.spoiler,
        Review.reviewDate,

        movie.movieID,
        movie.title,
        movie.poster,

        Users.username,
        Users.profilePicture

    FROM Review

    INNER JOIN movie
        ON Review.movieID = movie.movieID

    INNER JOIN Users
        ON Review.userID = Users.userID

    ORDER BY Review.reviewDate DESC
";

$result = $conn->query($sql);

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

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="review-page-container">


    <!-- BACK BUTTON -->

    <a
        href="search.php"
        class="back-button"
    >
        ←
    </a>



    <!-- PAGE TOP -->

    <section class="review-page-top">

        <div>

            <h1>
                Reviews
            </h1>

            <p>
                Discover what other movie lovers think.
            </p>

        </div>


        <a
            href="createReview.php"
            class="create-review-page-btn"
        >
            + Create Review
        </a>

    </section>



    <!-- SEARCH -->

    <div class="review-search">

        <input
            type="text"
            id="reviewSearch"
            placeholder="Search reviews by movie..."
        >

        <button
            type="button"
            id="reviewSearchBtn"
        >
            🔎
        </button>

    </div>



    <!-- REVIEWS -->

    <section class="review-feed">

        <?php if ($result->num_rows === 0): ?>

            <div class="no-reviews-message">

                <h2>
                    No reviews yet
                </h2>

                <p>
                    Be the first person to share your thoughts.
                </p>

            </div>

        <?php else: ?>


            <div id="reviewList">


                <?php while ($review = $result->fetch_assoc()): ?>


                    <article
                        class="review-feed-card"
                        data-movie="<?php echo htmlspecialchars($review["title"]); ?>"
                    >


                        <!-- MOVIE POSTER -->

                        <a
                            href="movieDetails.php?movie=<?php echo $review["movieID"]; ?>"
                            class="review-feed-poster"
                        >

                            <img
                                src="<?php echo htmlspecialchars($review["poster"]); ?>"
                                alt="<?php echo htmlspecialchars($review["title"]); ?>"
                            >

                        </a>



                        <!-- REVIEW CONTENT -->

                        <div class="review-feed-content">


                            <div class="review-feed-header">


                                <div>

                                    <h2>

                                        <?php echo htmlspecialchars($review["title"]); ?>

                                    </h2>


                                    <p class="review-by">

                                        by
                                        <strong>
                                            <?php echo htmlspecialchars($review["username"]); ?>
                                        </strong>

                                    </p>

                                </div>


                                <p class="review-date">

                                    <?php
                                    echo date(
                                        "M d, Y",
                                        strtotime($review["reviewDate"])
                                    );
                                    ?>

                                </p>


                            </div>



                            <!-- RATING -->

                            <div class="large-stars">

                                <?php

                                $rating = (float) $review["rating"];

                                $starRating = $rating / 2;

                                $fullStars = floor($starRating);

                                $hasHalfStar =
                                    ($starRating - $fullStars) == 0.5;

                                $emptyStars =
                                    5
                                    - $fullStars
                                    - ($hasHalfStar ? 1 : 0);


                                for (
                                    $i = 0;
                                    $i < $fullStars;
                                    $i++
                                ) {

                                    echo "★";

                                }


                                if ($hasHalfStar) {

                                    echo "⯨";

                                }


                                for (
                                    $i = 0;
                                    $i < $emptyStars;
                                    $i++
                                ) {

                                    echo "☆";

                                }

                                ?>

                                <span class="rating-number">

                                    <?php
                                    echo htmlspecialchars(
                                        $review["rating"]
                                    );
                                    ?>/10

                                </span>

                            </div>



                            <!-- REVIEW TEXT -->

                            <p
                                class="review-feed-text
                                <?php
                                echo $review["spoiler"]
                                    ? "spoiler-blurred"
                                    : "";
                                ?>"
                                <?php
                                if ($review["spoiler"]) {
                                    echo 'onclick="this.classList.toggle(\'spoiler-revealed\')"';
                                }
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $review["reviewText"]
                                );
                                ?>

                            </p>



                            <!-- SPOILER -->

                            <?php if ($review["spoiler"]): ?>

                                <p class="review-spoiler">

                                    ⚠ Contains spoilers
                                    <span>
                                        Click to reveal
                                    </span>

                                </p>

                            <?php endif; ?>



                            <!-- VIEW MOVIE -->

                            <a
                                href="movieDetails.php?movie=<?php echo $review["movieID"]; ?>"
                                class="view-movie-btn"
                            >
                                View Movie
                            </a>


                        </div>


                    </article>


                <?php endwhile; ?>


            </div>


            <!-- NO SEARCH RESULTS -->

            <p
                id="noReviewResults"
                class="no-results"
                style="display: none;"
            >
                No reviews found.
            </p>


        <?php endif; ?>


    </section>


</main>



<script>


const reviewSearch =
    document.getElementById("reviewSearch");


const reviewCards =
    document.querySelectorAll(".review-feed-card");


const noReviewResults =
    document.getElementById("noReviewResults");



/*
    SEARCH REVIEWS
*/

if (reviewSearch) {

    reviewSearch.addEventListener(
        "input",
        function() {

            const value =
                reviewSearch.value
                .toLowerCase()
                .trim();


            let foundReviews = 0;


            reviewCards.forEach(
                function(card) {

                    const movie =
                        card.dataset.movie
                        .toLowerCase();


                    if (
                        movie.includes(value)
                    ) {

                        card.style.display =
                            "flex";

                        foundReviews++;

                    }
                    else {

                        card.style.display =
                            "none";

                    }

                }
            );


            if (
                noReviewResults
            ) {

                if (foundReviews === 0) {

                    noReviewResults.style.display =
                        "block";

                }
                else {

                    noReviewResults.style.display =
                        "none";

                }

            }

        }
    );

}


</script>


</body>

</html>