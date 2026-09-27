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

                    ⭐ No rating yet

                    <span>
                        / 10
                    </span>

                </div>



                <p class="movie-overview">

                    <?php echo htmlspecialchars($movie['description']); ?>

                </p>



                <div class="movie-actions">


                    <button
                        type="button"
                        id="watchlistBtn"
                    >
                        + Watchlist
                    </button>


                    <button
                        type="button"
                        id="watchedBtn"
                    >
                        ✓ Watched
                    </button>


                    <a
                        href="writeReview.php?movie=<?php echo $movie['movieID']; ?>"
                        class="review-button"
                    >
                        Review
                    </a>


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


                <a
                    href="writeReview.php?movie=<?php echo $movie['movieID']; ?>"
                >
                    Write a Review
                </a>


            </div>


            <p>
                No reviews yet.
            </p>


        </section>


    </main>



    <script>


        const watchlistBtn =
            document.getElementById(
                "watchlistBtn"
            );


        const watchedBtn =
            document.getElementById(
                "watchedBtn"
            );



        watchlistBtn.addEventListener(
            "click",
            function() {

                watchlistBtn.textContent =
                    "✓ In Watchlist";

            }
        );



        watchedBtn.addEventListener(
            "click",
            function() {

                watchedBtn.textContent =
                    "✓ Watched";

                watchedBtn.disabled = true;

            }
        );


    </script>


</body>

</html>