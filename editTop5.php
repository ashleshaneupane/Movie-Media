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



        <!-- TOP 5 FORM -->

        <form
            id="top5Form"
            class="top5-form"
        >


            <!-- MOVIE 1 -->

            <div class="top5-field">

                <label for="movie1">
                    Movie 1:
                </label>

                <input
                    type="text"
                    id="movie1"
                    name="movie1"
                    value="Interstellar"
                    placeholder="Search movie..."
                >

                <span class="movie-search-icon">
                    🔍
                </span>

            </div>



            <!-- MOVIE 2 -->

            <div class="top5-field">

                <label for="movie2">
                    Movie 2:
                </label>

                <input
                    type="text"
                    id="movie2"
                    name="movie2"
                    value="Spider-Man"
                    placeholder="Search movie..."
                >

                <span class="movie-search-icon">
                    🔍
                </span>

            </div>



            <!-- MOVIE 3 -->

            <div class="top5-field">

                <label for="movie3">
                    Movie 3:
                </label>

                <input
                    type="text"
                    id="movie3"
                    name="movie3"
                    value="Avatar"
                    placeholder="Search movie..."
                >

                <span class="movie-search-icon">
                    🔍
                </span>

            </div>



            <!-- MOVIE 4 -->

            <div class="top5-field">

                <label for="movie4">
                    Movie 4:
                </label>

                <input
                    type="text"
                    id="movie4"
                    name="movie4"
                    value="Harry Potter"
                    placeholder="Search movie..."
                >

                <span class="movie-search-icon">
                    🔍
                </span>

            </div>



            <!-- MOVIE 5 -->

            <div class="top5-field">

                <label for="movie5">
                    Movie 5:
                </label>

                <input
                    type="text"
                    id="movie5"
                    name="movie5"
                    value="Lucifer"
                    placeholder="Search movie..."
                >

                <span class="movie-search-icon">
                    🔍
                </span>

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



    <script>

        const top5Form =
            document.getElementById("top5Form");


        top5Form.addEventListener(
            "submit",
            function(event) {

                event.preventDefault();


                const movie1 =
                    document.getElementById("movie1")
                    .value
                    .trim();


                if (movie1 === "") {

                    alert(
                        "Please enter at least your first movie."
                    );

                    return;

                }


                alert(
                    "Top 5 updated successfully!"
                );


                window.location.href =
                    "account.php";

            }
        );

    </script>


</body>

</html>