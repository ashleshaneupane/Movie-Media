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


        <!-- TOP -->

        <section class="review-page-top">


            <div>

                <h1>
                    Reviews
                </h1>

                <p>
                    Share your thoughts and discover what
                    other movie lovers think.
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
                placeholder="Search reviews..."
            >

            <button
                type="button"
                id="reviewSearchBtn"
            >
                🔎
            </button>

        </div>



        <!-- CONTENT -->

        <div class="review-layout">


            <!-- YOUR REVIEWS -->

            <section class="my-reviews">

                <h2>
                    My Reviews
                </h2>


                <article
                    class="review-feed-card"
                    data-movie="Interstellar"
                >

                    <div class="review-feed-poster">
                        INTERSTELLAR
                    </div>


                    <div class="review-feed-content">

                        <h3>
                            Interstellar
                        </h3>

                        <div class="large-stars">
                            ★★★★★
                        </div>

                        <p>
                            One of the best sci-fi movies
                            I've ever watched.
                        </p>


                        <a
                            href="movieDetails.php?movie=interstellar"
                        >
                            View Movie
                        </a>

                    </div>

                </article>



                <article
                    class="review-feed-card"
                    data-movie="Inception"
                >

                    <div class="review-feed-poster">
                        INCEPTION
                    </div>


                    <div class="review-feed-content">

                        <h3>
                            Inception
                        </h3>

                        <div class="large-stars">
                            ★★★★☆
                        </div>

                        <p>
                            A brilliant and complicated
                            story with amazing visuals.
                        </p>


                        <a
                            href="movieDetails.php?movie=inception"
                        >
                            View Movie
                        </a>

                    </div>

                </article>


            </section>



            <!-- COMMUNITY -->

            <aside class="community-reviews">

                <h2>
                    Reviews from the Community
                </h2>


                <article class="community-review">

                    <div class="community-poster">
                        O
                    </div>


                    <div>

                        <strong>
                            Oppenheimer
                        </strong>

                        <div class="community-stars">
                            ★★★★★
                        </div>

                    </div>

                </article>



                <article class="community-review">

                    <div class="community-poster">
                        A
                    </div>


                    <div>

                        <strong>
                            Avatar
                        </strong>

                        <div class="community-stars">
                            ★★★★☆
                        </div>

                    </div>

                </article>



                <article class="community-review">

                    <div class="community-poster">
                        D
                    </div>


                    <div>

                        <strong>
                            Dune
                        </strong>

                        <div class="community-stars">
                            ★★★★★
                        </div>

                    </div>

                </article>


            </aside>


        </div>


    </main>



    <script>

        const reviewSearch =
            document.getElementById(
                "reviewSearch"
            );


        const reviewCards =
            document.querySelectorAll(
                ".review-feed-card"
            );


        reviewSearch.addEventListener(
            "input",
            function() {

                const value =
                    reviewSearch.value
                    .toLowerCase()
                    .trim();


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

                        } else {

                            card.style.display =
                                "none";

                        }

                    }
                );

            }
        );

    </script>


</body>

</html>