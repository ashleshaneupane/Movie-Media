<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Search Movies - Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="search-container">


        <!-- PAGE TITLE -->

        <div class="search-heading">

            <h1>
                Discover Movies
            </h1>

            <p>
                Find movies, discover something new and share your reviews.
            </p>

        </div>



        <!-- SEARCH CONTROLS -->

        <section class="search-controls">


            <div class="search-box">

                <input
                    type="text"
                    id="movieSearch"
                    placeholder="Search movies..."
                >

                <button
                    type="button"
                    id="searchButton"
                >
                    🔎
                </button>

            </div>



            <!-- RANDOM MOVIE -->

            <a
                href="randomMovie.php"
                class="random-movie-btn"
            >
                🎲 Random Movie
            </a>



            <!-- CREATE REVIEW -->

            <a
                href="createReview.php"
                class="create-review-btn"
            >
                ✍️ Create Review
            </a>


        </section>



        <!-- GENRE -->

        <div class="genre-section">

            <label for="genreFilter">
                Genre
            </label>

            <select id="genreFilter">

                <option value="all">
                    All Genres
                </option>

                <option value="Sci-Fi">
                    Sci-Fi
                </option>

                <option value="Thriller">
                    Thriller
                </option>

                <option value="Drama">
                    Drama
                </option>

                <option value="Action">
                    Action
                </option>

                <option value="Romance">
                    Romance
                </option>

                <option value="Horror">
                    Horror
                </option>

            </select>

        </div>



        <!-- SEARCH RESULTS -->

        <section class="search-results">

            <h2>
                Movies
            </h2>


            <div
                class="movie-grid"
                id="movieGrid"
            >


                <!-- MOVIE 1 -->

                <article
                    class="movie-card"
                    data-title="Interstellar"
                    data-genre="Sci-Fi"
                >

                    <a href="movieDetails.php?movie=interstellar">

                        <div class="movie-poster">
                            INTERSTELLAR
                        </div>

                    </a>


                    <div class="movie-card-info">

                        <h3>
                            Interstellar
                        </h3>

                        <p>
                            Sci-Fi • 2014
                        </p>

                        <span>
                            ⭐ 8.7
                        </span>

                    </div>

                </article>



                <!-- MOVIE 2 -->

                <article
                    class="movie-card"
                    data-title="Inception"
                    data-genre="Thriller"
                >

                    <a href="movieDetails.php?movie=inception">

                        <div class="movie-poster">
                            INCEPTION
                        </div>

                    </a>


                    <div class="movie-card-info">

                        <h3>
                            Inception
                        </h3>

                        <p>
                            Thriller • 2010
                        </p>

                        <span>
                            ⭐ 8.8
                        </span>

                    </div>

                </article>



                <!-- MOVIE 3 -->

                <article
                    class="movie-card"
                    data-title="Oppenheimer"
                    data-genre="Drama"
                >

                    <a href="movieDetails.php?movie=oppenheimer">

                        <div class="movie-poster">
                            OPPENHEIMER
                        </div>

                    </a>


                    <div class="movie-card-info">

                        <h3>
                            Oppenheimer
                        </h3>

                        <p>
                            Drama • 2023
                        </p>

                        <span>
                            ⭐ 8.6
                        </span>

                    </div>

                </article>



                <!-- MOVIE 4 -->

                <article
                    class="movie-card"
                    data-title="Avatar"
                    data-genre="Action"
                >

                    <a href="movieDetails.php?movie=avatar">

                        <div class="movie-poster">
                            AVATAR
                        </div>

                    </a>


                    <div class="movie-card-info">

                        <h3>
                            Avatar
                        </h3>

                        <p>
                            Action • 2009
                        </p>

                        <span>
                            ⭐ 7.5
                        </span>

                    </div>

                </article>



                <!-- MOVIE 5 -->

                <article
                    class="movie-card"
                    data-title="The Notebook"
                    data-genre="Romance"
                >

                    <a href="movieDetails.php?movie=notebook">

                        <div class="movie-poster">
                            THE NOTEBOOK
                        </div>

                    </a>


                    <div class="movie-card-info">

                        <h3>
                            The Notebook
                        </h3>

                        <p>
                            Romance • 2004
                        </p>

                        <span>
                            ⭐ 7.8
                        </span>

                    </div>

                </article>



                <!-- MOVIE 6 -->

                <article
                    class="movie-card"
                    data-title="The Conjuring"
                    data-genre="Horror"
                >

                    <a href="movieDetails.php?movie=conjuring">

                        <div class="movie-poster">
                            THE CONJURING
                        </div>

                    </a>


                    <div class="movie-card-info">

                        <h3>
                            The Conjuring
                        </h3>

                        <p>
                            Horror • 2013
                        </p>

                        <span>
                            ⭐ 7.5
                        </span>

                    </div>

                </article>


            </div>



            <!-- NO RESULTS -->

            <p
                id="noResults"
                class="no-results"
            >
                No movies found.
            </p>


        </section>


    </main>



    <script>

        const movieSearch =
            document.getElementById("movieSearch");


        const searchButton =
            document.getElementById("searchButton");


        const genreFilter =
            document.getElementById("genreFilter");


        const movieCards =
            document.querySelectorAll(".movie-card");


        const noResults =
            document.getElementById("noResults");



        function filterMovies() {

            const searchValue =
                movieSearch.value
                .toLowerCase()
                .trim();


            const selectedGenre =
                genreFilter.value;


            let foundMovies = 0;


            movieCards.forEach(function(card) {

                const title =
                    card.dataset.title
                    .toLowerCase();


                const genre =
                    card.dataset.genre;


                const matchesSearch =
                    title.includes(searchValue);


                const matchesGenre =
                    selectedGenre === "all" ||
                    genre === selectedGenre;


                if (
                    matchesSearch &&
                    matchesGenre
                ) {

                    card.style.display = "block";

                    foundMovies++;

                } else {

                    card.style.display = "none";

                }

            });


            if (foundMovies === 0) {

                noResults.style.display = "block";

            } else {

                noResults.style.display = "none";

            }

        }



        /* SEARCH BUTTON */

        searchButton.addEventListener(
            "click",
            filterMovies
        );



        /* LIVE SEARCH */

        movieSearch.addEventListener(
            "input",
            filterMovies
        );



        /* GENRE FILTER */

        genreFilter.addEventListener(
            "change",
            filterMovies
        );

    </script>


</body>

</html>