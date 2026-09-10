<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Movie Details - Movie Media</title>

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
                INTERSTELLAR
            </div>


            <div class="movie-main-info">

                <h1>
                    Interstellar
                </h1>


                <p class="movie-meta">
                    2014 • Sci-Fi • Drama • Adventure
                </p>


                <div class="movie-rating">

                    ⭐ 8.7

                    <span>
                        / 10
                    </span>

                </div>


                <p class="movie-overview">

                    A team of explorers travel through a
                    wormhole in space in an attempt to ensure
                    humanity's survival.

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
                        href="createReview.php?movie=interstellar"
                        class="review-movie-btn"
                    >
                        ★ Review
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
                    <strong>Director:</strong>
                    Christopher Nolan
                </p>

                <p>
                    <strong>Release:</strong>
                    2014
                </p>

                <p>
                    <strong>Genre:</strong>
                    Sci-Fi, Drama, Adventure
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
                    href="createReview.php?movie=interstellar"
                >
                    Write a Review
                </a>

            </div>



            <article class="movie-review">

                <div class="review-user">

                    <div class="small-profile">
                        A
                    </div>

                    <strong>
                        Ash Neupane
                    </strong>

                </div>


                <div class="review-rating">
                    ★★★★★
                </div>


                <p>
                    One of the best sci-fi movies I've watched.
                    The visuals and story are incredible.
                </p>

            </article>



            <article class="movie-review">

                <div class="review-user">

                    <div class="small-profile">
                        M
                    </div>

                    <strong>
                        Maya
                    </strong>

                </div>


                <div class="review-rating">
                    ★★★★☆
                </div>


                <p>
                    Beautiful movie with an emotional story.
                </p>

            </article>


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