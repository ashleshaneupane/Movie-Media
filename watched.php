<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/*
    Get movies watched by the logged-in user
*/

$watchedQuery = $conn->prepare(
    "SELECT
        Watched.watchedID,
        Watched.movieID,
        Watched.watchedDate,
        movie.title,
        movie.poster
     FROM Watched
     INNER JOIN movie
        ON Watched.movieID = movie.movieID
     WHERE Watched.userID = ?
     ORDER BY Watched.watchedDate DESC"
);

$watchedQuery->bind_param("i", $userID);
$watchedQuery->execute();

$watchedResult = $watchedQuery->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Watched - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="watched-container">


    <!-- BACK BUTTON -->

    <a
        href="account.php"
        class="back-button"
    >
        ←
    </a>


    <!-- TITLE -->

    <h1 class="account-page-title">
        Watched
    </h1>


    <!-- MOVIE GRID -->

    <section class="watched-grid">

        <?php if ($watchedResult->num_rows > 0): ?>

            <?php while ($movie = $watchedResult->fetch_assoc()): ?>

                <article class="watched-card" data-watched-id="<?php echo $movie["watchedID"]; ?>">

    <div class="watched-poster">
        <img src="<?php echo htmlspecialchars($movie["poster"]); ?>" 
             alt="<?php echo htmlspecialchars($movie["title"]); ?>">
    </div>

    <h2><?php echo htmlspecialchars($movie["title"]); ?></h2>

    <p class="watched-date">
        Watched: <?php echo htmlspecialchars($movie["watchedDate"]); ?>
    </p>

    <button type="button"
            class="remove-watched-btn"
            data-watched-id="<?php echo $movie["watchedID"]; ?>">
        Remove
    </button>

</article>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty-watched">

                <h2>
                    No watched movies yet
                </h2>

                <p>
                    Movies you mark as watched will appear here.
                </p>

            </div>

        <?php endif; ?>

    </section>


</main>

<script>

const removeButtons = document.querySelectorAll(".remove-watched-btn");

removeButtons.forEach(function(button) {

    button.addEventListener("click", function() {

        const watchedID = button.dataset.watchedId;

        const formData = new FormData();
        formData.append("watchedID", watchedID);

        fetch("removeWatched.php", {
            method: "POST",
            body: formData
        })

        .then(function(response) {
            return response.text();
        })

        .then(function(result) {

            console.log("Remove watched result:", result);

            if (result === "success") {

                const card = button.closest(".watched-card");

                card.remove();

            } else {

                console.log("Could not remove movie:", result);

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