<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/*
    Get all movies from the movie table
*/

$movieQuery = $conn->query(
    "SELECT movieID, title
     FROM movie
     ORDER BY title ASC"
);

$movies = [];

while ($movie = $movieQuery->fetch_assoc()) {
    $movies[] = $movie;
}


/*
    Get user's current Top 5
*/

$top5Query = $conn->prepare(
    "SELECT position, movieID
     FROM UserTopMovies
     WHERE userID = ?
     ORDER BY position ASC"
);

$top5Query->bind_param("i", $userID);
$top5Query->execute();

$top5Result = $top5Query->get_result();

$top5Movies = [];

while ($row = $top5Result->fetch_assoc()) {
    $top5Movies[$row["position"]] = $row["movieID"];
}


/*
    Save Top 5
*/

$saveMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $selectedMovies = [
        1 => (int) ($_POST["movie1"] ?? 0),
        2 => (int) ($_POST["movie2"] ?? 0),
        3 => (int) ($_POST["movie3"] ?? 0),
        4 => (int) ($_POST["movie4"] ?? 0),
        5 => (int) ($_POST["movie5"] ?? 0)
    ];


    /*
        Remove empty selections
    */

    $selectedMovies = array_filter(
        $selectedMovies,
        function ($movieID) {
            return $movieID > 0;
        }
    );


    /*
        Check for duplicate movies
    */

    if (count($selectedMovies) !== count(array_unique($selectedMovies))) {

        $saveMessage = "You cannot select the same movie twice.";

    } else {

        /*
            Delete the user's old Top 5
        */

        $deleteTop5 = $conn->prepare(
            "DELETE FROM UserTopMovies
             WHERE userID = ?"
        );

        $deleteTop5->bind_param("i", $userID);
        $deleteTop5->execute();


        /*
            Insert the new Top 5
        */

        $insertTop5 = $conn->prepare(
            "INSERT INTO UserTopMovies
             (userID, movieID, position)
             VALUES (?, ?, ?)"
        );


        foreach ($selectedMovies as $position => $movieID) {

            $insertTop5->bind_param(
                "iii",
                $userID,
                $movieID,
                $position
            );

            $insertTop5->execute();
        }


        /*
            Return to Account after saving
        */

        header("Location: account.php?top5=updated");
        exit;
    }
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

    <title>Edit Top 5 - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="edit-top5-container">


        <!-- BACK BUTTON -->

        <a
            href="account.php"
            class="back-button"
        >
            ←
        </a>



        <!-- TITLE -->

        <h1 class="edit-top5-title">
            EDIT TOP 5
        </h1>



        <?php if ($saveMessage !== ""): ?>

            <p
                style="
                    color: #ff7777;
                    text-align: center;
                    margin-bottom: 20px;
                "
            >
                <?php echo htmlspecialchars($saveMessage); ?>
            </p>

        <?php endif; ?>



        <!-- TOP 5 FORM -->

        <form
            id="top5Form"
            class="top5-form"
            method="POST"
            action="editTop5.php"
        >


            <!-- MOVIE 1 -->

            <div class="top5-field">

                <label for="movie1">
                    Movie 1:
                </label>

                <select
                    id="movie1"
                    name="movie1"
                >

                    <option value="">
                        Select a movie
                    </option>

                    <?php foreach ($movies as $movie): ?>

                        <option
                            value="<?php echo $movie["movieID"]; ?>"
                            <?php
                            echo (
                                ($top5Movies[1] ?? 0) == $movie["movieID"]
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            <?php echo htmlspecialchars($movie["title"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <!-- MOVIE 2 -->

            <div class="top5-field">

                <label for="movie2">
                    Movie 2:
                </label>

                <select
                    id="movie2"
                    name="movie2"
                >

                    <option value="">
                        Select a movie
                    </option>

                    <?php foreach ($movies as $movie): ?>

                        <option
                            value="<?php echo $movie["movieID"]; ?>"
                            <?php
                            echo (
                                ($top5Movies[2] ?? 0) == $movie["movieID"]
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            <?php echo htmlspecialchars($movie["title"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <!-- MOVIE 3 -->

            <div class="top5-field">

                <label for="movie3">
                    Movie 3:
                </label>

                <select
                    id="movie3"
                    name="movie3"
                >

                    <option value="">
                        Select a movie
                    </option>

                    <?php foreach ($movies as $movie): ?>

                        <option
                            value="<?php echo $movie["movieID"]; ?>"
                            <?php
                            echo (
                                ($top5Movies[3] ?? 0) == $movie["movieID"]
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            <?php echo htmlspecialchars($movie["title"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <!-- MOVIE 4 -->

            <div class="top5-field">

                <label for="movie4">
                    Movie 4:
                </label>

                <select
                    id="movie4"
                    name="movie4"
                >

                    <option value="">
                        Select a movie
                    </option>

                    <?php foreach ($movies as $movie): ?>

                        <option
                            value="<?php echo $movie["movieID"]; ?>"
                            <?php
                            echo (
                                ($top5Movies[4] ?? 0) == $movie["movieID"]
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            <?php echo htmlspecialchars($movie["title"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <!-- MOVIE 5 -->

            <div class="top5-field">

                <label for="movie5">
                    Movie 5:
                </label>

                <select
                    id="movie5"
                    name="movie5"
                >

                    <option value="">
                        Select a movie
                    </option>

                    <?php foreach ($movies as $movie): ?>

                        <option
                            value="<?php echo $movie["movieID"]; ?>"
                            <?php
                            echo (
                                ($top5Movies[5] ?? 0) == $movie["movieID"]
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            <?php echo htmlspecialchars($movie["title"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>



            <!-- SAVE -->

            <button
                type="submit"
                class="save-top5-btn"
            >
                SAVE
            </button>


        </form>


    </main>

</body>

</html>