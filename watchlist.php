<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/*
    Get movies in user's watchlist
*/

$watchlistQuery = $conn->prepare(
    "SELECT
        Watchlist.watchlistID,
        Watchlist.movieID,
        movie.title,
        movie.genre,
        movie.releaseDate,
        movie.poster
     FROM Watchlist
     INNER JOIN movie
        ON Watchlist.movieID = movie.movieID
     WHERE Watchlist.userID = ?
     ORDER BY Watchlist.addedDate DESC"
);

$watchlistQuery->bind_param("i", $userID);
$watchlistQuery->execute();

$watchlistResult = $watchlistQuery->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Watchlist - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="watchlist-container">


    <!-- BACK BUTTON -->

    <a
        href="account.php"
        class="back-button"
    >
        ←
    </a>


    <!-- TITLE -->

    <h1 class="account-page-title">
        Watchlist
    </h1>


    <!-- WATCHLIST -->

    <section class="watchlist-items">

        <?php if ($watchlistResult->num_rows > 0): ?>

            <?php while ($movie = $watchlistResult->fetch_assoc()): ?>

                <article class="watchlist-item">

                    <div class="watchlist-poster">

                        <img
                            src="<?php echo htmlspecialchars($movie["poster"]); ?>"
                            alt="<?php echo htmlspecialchars($movie["title"]); ?>"
                        >

                    </div>


                    <div class="watchlist-info">

                        <h2>
                            <?php echo htmlspecialchars($movie["title"]); ?>
                        </h2>

                        <p>
                            <?php echo htmlspecialchars($movie["genre"]); ?>
                        </p>

                        <p>
                            <?php
                            echo date(
                                "Y",
                                strtotime($movie["releaseDate"])
                            );
                            ?>
                        </p>


                        <div class="watchlist-actions">

                            <button
                                type="button"
                                class="remove-btn"
                                data-watchlist-id="<?php echo $movie["watchlistID"]; ?>"
                            >
                                Remove
                            </button>


                            <button
                                type="button"
                                class="watched-btn"
                                data-movie-id="<?php echo $movie["movieID"]; ?>"
                            >
                                Mark as Watched
                            </button>

                        </div>

                    </div>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty-watchlist">

                <h2>
                    Your watchlist is empty
                </h2>

                <p>
                    Add movies you want to watch later.
                </p>

            </div>

        <?php endif; ?>

    </section>


</main>
<script>

const watchedButtons =
    document.querySelectorAll(".watched-btn");


watchedButtons.forEach(function(button) {

    button.addEventListener("click", function() {

        const movieID = button.dataset.movieId;

        const formData = new FormData();

        formData.append("movieID", movieID);


        fetch("markWatched.php", {
            method: "POST",
            body: formData
        })

        .then(function(response) {
            return response.text();
        })

        .then(function(result) {

            console.log("Watched result:", result);


            if (result === "success") {

                button.textContent = "✓ Watched";
                button.disabled = true;

                /*
                    Remove the movie from Watchlist
                */

                setTimeout(function() {

                    button.closest(".watchlist-item").remove();

                }, 500);

            }

            else if (result === "exists") {

                button.textContent = "✓ Already Watched";
                button.disabled = true;

            }

            else {

                console.log("Watched error:", result);

            }

        })

        .catch(function(error) {

            console.log("Error:", error);

        });

    });

});
const removeButtons =
    document.querySelectorAll(".remove-btn");

removeButtons.forEach(function(button) {

    button.addEventListener("click", function() {

        const watchlistID = button.dataset.watchlistId;

        console.log("Watchlist ID:", watchlistID);

        const formData = new FormData();

        formData.append("watchlistID", watchlistID);

        fetch("removeWatchlist.php", {
            method: "POST",
            body: formData
        })

        .then(function(response) {
            return response.text();
        })

        .then(function(result) {

            console.log("Remove result:", result);

            if (result === "success") {

                button.closest(".watchlist-item").remove();

            } else {

                console.log("Remove failed:", result);

            }

        })

        .catch(function(error) {

            console.log("Error:", error);

        });

    });

});

</script>

</body>

</html>