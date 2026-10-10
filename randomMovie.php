<?php

include 'includes/auth.php';

include 'includes/config.php';


/*
 * Get one random movie from the database
 */

$userID = (int) $_SESSION["userID"];

$sql = "
    SELECT
        movie.*,
        COALESCE(AVG(Review.rating), 0) AS averageRating,
        COUNT(Review.reviewID) AS reviewCount
    FROM movie
    LEFT JOIN Review
        ON movie.movieID = Review.movieID
    WHERE NOT EXISTS (
        SELECT 1
        FROM Watched
        WHERE Watched.movieID = movie.movieID
        AND Watched.userID = ?
    )
    GROUP BY movie.movieID
    ORDER BY RAND()
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();

$result = $stmt->get_result();


if (!$result) {
    die("Something went wrong while finding a random movie.");
}


if ($result->num_rows === 0) {
    $movie = null;
} else {
    $movie = $result->fetch_assoc();
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

    <title>Random Movie - Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="random-movie-container">


        <!-- BACK BUTTON -->

        <a
            href="search.php"
            class="back-button"
        >
            ←
        </a>


        <?php if ($movie): ?>


            <!-- HEADING -->

            <div class="random-heading">

                <p class="random-label">
                    MOVIE MEDIA
                </p>


                <h1>
                    What should you watch?
                </h1>


                <p>
                    Here's a random movie for you.
                </p>

            </div>



            <!-- RANDOM MOVIE CARD -->

            <section class="random-movie-card">


                <!-- POSTER -->

                <div class="random-movie-poster">

                    <img
                        src="<?php echo htmlspecialchars($movie['poster']); ?>"
                        alt="<?php echo htmlspecialchars($movie['title']); ?>"
                    >

                </div>



                <!-- INFORMATION -->

                <div class="random-movie-info">


                    <h2>

                        <?php
                        echo htmlspecialchars($movie['title']);
                        ?>

                    </h2>



                    <p class="random-movie-meta">

                        <?php
                        echo htmlspecialchars($movie['releaseDate']);
                        ?>

                        •

                        <?php
                        echo htmlspecialchars($movie['genre']);
                        ?>

                    </p>



                    <p class="random-movie-meta">

                        <?php
                        echo htmlspecialchars($movie['language']);
                        ?>

                        •

                        <?php
                        echo htmlspecialchars($movie['runtime']);
                        ?>

                        minutes

                    </p>



                   <div class="random-movie-rating">

    <?php if ((int)$movie['reviewCount'] > 0): ?>

        ⭐ <?php echo number_format((float)$movie['averageRating'], 1); ?> / 10
        <span>
            (<?php echo (int)$movie['reviewCount']; ?>
            <?php echo ((int)$movie['reviewCount'] === 1) ? 'review' : 'reviews'; ?>)
        </span>

    <?php else: ?>

        ⭐ No rating yet

    <?php endif; ?>

</div>



                    <p class="random-movie-overview">

                        <?php
                        echo htmlspecialchars($movie['description']);
                        ?>

                    </p>



                    <!-- BUTTONS -->

                    <div class="random-movie-actions">


                        <a
                            href="movieDetails.php?movie=<?php echo $movie['movieID']; ?>"
                            class="random-view-btn"
                        >
                            View Movie Details
                        </a>


                        <a
                            href="randomMovie.php"
                            class="random-again-btn"
                        >
                            🎲 Try Another
                        </a>


                    </div>


                </div>


            </section>


        <?php else: ?>


            <!-- EMPTY STATE -->

            <section class="random-empty">


                <div class="random-empty-icon">
                    🎬
                </div>


                <h1>
                    No Movies Found
                </h1>


                <p>
                    There are currently no movies available
                    in Movie Media.
                </p>


                <a
                    href="search.php"
                    class="random-view-btn"
                >
                    Back to Search
                </a>


            </section>


        <?php endif; ?>


    </main>


</body>

</html>