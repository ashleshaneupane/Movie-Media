<?php
include 'includes/auth.php';
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


            <!-- MOVIE 1 -->

            <article class="watchlist-item">

                <div class="watchlist-poster">
                    1
                </div>


                <div class="watchlist-info">

                    <h2>
                        Interstellar
                    </h2>

                    <p>
                        Sci-Fi
                    </p>

                    <p>
                        2014
                    </p>


                    <div class="watchlist-actions">

                        <button
                            type="button"
                            class="remove-btn"
                        >
                            Remove
                        </button>

                        <button
                            type="button"
                            class="watched-btn"
                        >
                            Mark as Watched
                        </button>

                    </div>

                </div>

            </article>



            <!-- MOVIE 2 -->

            <article class="watchlist-item">

                <div class="watchlist-poster">
                    2
                </div>


                <div class="watchlist-info">

                    <h2>
                        Inception
                    </h2>

                    <p>
                        Sci-Fi / Thriller
                    </p>

                    <p>
                        2010
                    </p>


                    <div class="watchlist-actions">

                        <button
                            type="button"
                            class="remove-btn"
                        >
                            Remove
                        </button>

                        <button
                            type="button"
                            class="watched-btn"
                        >
                            Mark as Watched
                        </button>

                    </div>

                </div>

            </article>



            <!-- MOVIE 3 -->

            <article class="watchlist-item">

                <div class="watchlist-poster">
                    3
                </div>


                <div class="watchlist-info">

                    <h2>
                        Oppenheimer
                    </h2>

                    <p>
                        Drama / History
                    </p>

                    <p>
                        2023
                    </p>


                    <div class="watchlist-actions">

                        <button
                            type="button"
                            class="remove-btn"
                        >
                            Remove
                        </button>

                        <button
                            type="button"
                            class="watched-btn"
                        >
                            Mark as Watched
                        </button>

                    </div>

                </div>

            </article>


        </section>


    </main>



    <script>

        const removeButtons =
            document.querySelectorAll(".remove-btn");


        removeButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    const movie =
                        button.closest(".watchlist-item");

                    movie.remove();

                }
            );

        });



        const watchedButtons =
            document.querySelectorAll(".watched-btn");


        watchedButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    button.textContent =
                        "Watched";

                    button.disabled = true;

                    button.style.opacity = "0.5";

                }
            );

        });

    </script>


</body>

</html>