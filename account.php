<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/*
    Get user information
*/

$userQuery = $conn->prepare(
    "SELECT name, username, profilePicture, bio, favoriteGenre
     FROM Users
     WHERE userID = ?"
);

$userQuery->bind_param("i", $userID);
$userQuery->execute();

$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();


/*
    Get user's Top 5 movies
*/

$top5Query = $conn->prepare(
    "SELECT
        UserTopMovies.position,
        movie.movieID,
        movie.title,
        movie.poster
     FROM UserTopMovies
     INNER JOIN movie
        ON UserTopMovies.movieID = movie.movieID
     WHERE UserTopMovies.userID = ?
     ORDER BY UserTopMovies.position ASC"
);

$top5Query->bind_param("i", $userID);
$top5Query->execute();

$top5Result = $top5Query->get_result();

$top5Movies = [];

while ($row = $top5Result->fetch_assoc()) {

    $top5Movies[$row["position"]] = $row;

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Account - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="account-container">


    <!-- =========================
         PROFILE SECTION
    ========================== -->

    <section class="account-profile">

        <div class="profile-picture">

            <?php if (!empty($user["profilePicture"])): ?>

                <img
                    src="<?php echo htmlspecialchars($user["profilePicture"]); ?>"
                    alt="Profile Picture"
                >

            <?php else: ?>

                <?php echo strtoupper(substr($user["name"], 0, 1)); ?>

            <?php endif; ?>

        </div>


        <div class="profile-info">

            <h2 class="profile-name">
                <?php echo htmlspecialchars($user["name"]); ?>
            </h2>

            <p class="profile-username">
                @<?php echo htmlspecialchars($user["username"]); ?>
            </p>


            <?php if (!empty($user["bio"])): ?>

                <p class="profile-bio">
                    <?php echo htmlspecialchars($user["bio"]); ?>
                </p>

            <?php endif; ?>


            <?php if (!empty($user["favoriteGenre"])): ?>

                <p class="profile-genre">
                    Favorite Genre:
                    <?php echo htmlspecialchars($user["favoriteGenre"]); ?>
                </p>

            <?php endif; ?>

        </div>


        <a href="editProfile.php" class="edit-profile-btn">
            Edit Profile
        </a>

    </section>



    <!-- =========================
         ACCOUNT NAVIGATION
    ========================== -->

    <nav class="account-nav">

        <a href="watched.php">
            Watched
        </a>

        <a href="reviews.php">
            Reviews
        </a>

        <a href="watchlist.php">
            Watchlist
        </a>

    </nav>



    <!-- =========================
         TOP 5 SECTION
    ========================== -->

    <section class="top-five-section">

        <div class="top-five-heading">

            <h2>
                @<?php echo htmlspecialchars($user["username"]); ?>'s Top 5 Movies/TV Shows of All Time
            </h2>

            <a
                href="editTop5.php"
                class="edit-top-five-btn"
            >
                EDIT
            </a>

        </div>



        <!-- =========================
             TOP 5 MOVIES
        ========================== -->

        <div class="top-five-list">

            <?php for ($position = 1; $position <= 5; $position++): ?>

                <div class="top-five-card">

                    <?php if (isset($top5Movies[$position])): ?>

                        <div class="movie-poster">

                            <img
                                src="<?php echo htmlspecialchars($top5Movies[$position]["poster"]); ?>"
                                alt="<?php echo htmlspecialchars($top5Movies[$position]["title"]); ?>"
                            >

                        </div>

                        <h3>
                            <?php echo htmlspecialchars($top5Movies[$position]["title"]); ?>
                        </h3>

                    <?php else: ?>

                        <div class="movie-poster">
                            <?php echo $position; ?>
                        </div>

                        <h3>
                            No movie selected
                        </h3>

                    <?php endif; ?>

                </div>

            <?php endfor; ?>

        </div>

    </section>


    <a href="logout.php" class="logout-btn">
        Logout
    </a>

</main>

</body>

</html>