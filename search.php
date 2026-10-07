
<?php

include 'includes/auth.php';
include 'includes/config.php';


/*
    Get all movies with their average rating
*/

$sql = "
    SELECT
        movie.*,
        AVG(Review.rating) AS averageRating
    FROM movie
    LEFT JOIN Review
        ON movie.movieID = Review.movieID
    GROUP BY movie.movieID
    ORDER BY movie.title ASC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Search Movies - Movie Media
    </title>

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



        <!-- REVIEWS -->

        <a
            href="review.php"
            class="reviews-btn"
        >
            ⭐ Reviews
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


            <?php

            if ($result->num_rows > 0) {

                while ($movie = $result->fetch_assoc()) {

            ?>


                <article
                    class="movie-card"
                    data-title="<?php
                        echo htmlspecialchars(
                            $movie['title']
                        );
                    ?>"
                    data-genre="<?php
                        echo htmlspecialchars(
                            $movie['genre']
                        );
                    ?>"
                >


                    <a
                        href="movieDetails.php?movie=<?php
                            echo $movie['movieID'];
                        ?>"
                    >

                        <div class="movie-poster">

                            <img
                                src="<?php
                                    echo htmlspecialchars(
                                        $movie['poster']
                                    );
                                ?>"
                                alt="<?php
                                    echo htmlspecialchars(
                                        $movie['title']
                                    );
                                ?>"
                            >

                        </div>

                    </a>


                    <div class="movie-card-info">


                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $movie['title']
                            );
                            ?>

                        </h3>


                        <p>

                            <?php
                            echo htmlspecialchars(
                                $movie['genre']
                            );
                            ?>

                            •

                            <?php
                            echo htmlspecialchars(
                                $movie['releaseDate']
                            );
                            ?>

                        </p>


                        <!-- MOVIE RATING -->

                        <span>

                            <?php if (
                                $movie['averageRating'] === null
                            ): ?>

                                ⭐ No rating yet

                            <?php else: ?>

                                ⭐

                                <?php
                                echo number_format(
                                    (float)
                                    $movie['averageRating'],
                                    1
                                );
                                ?>

                                / 10

                            <?php endif; ?>

                        </span>


                    </div>


                </article>


            <?php

                }

            } else {

            ?>

                <p>
                    No movies found.
                </p>

            <?php

            }

            ?>


        </div>



        <!-- NO RESULTS -->

        <p
            id="noResults"
            class="no-results"
            style="display: none;"
        >
            No movies found.
        </p>



        <!-- PAGINATION -->

        <div
            class="user-pagination"
            id="moviePagination"
        ></div>


    </section>


</main>



<script>


/* =========================
   PAGINATION SETTINGS
========================= */

const moviesPerPage = 8;

let currentPage = 1;



/* =========================
   ELEMENTS
========================= */

const movieSearch =
    document.getElementById(
        "movieSearch"
    );


const searchButton =
    document.getElementById(
        "searchButton"
    );


const genreFilter =
    document.getElementById(
        "genreFilter"
    );


const movieGrid =
    document.getElementById(
        "movieGrid"
    );


const movieCards =
    Array.from(
        document.querySelectorAll(
            ".movie-card"
        )
    );


const noResults =
    document.getElementById(
        "noResults"
    );


const moviePagination =
    document.getElementById(
        "moviePagination"
    );



/* =========================
   FILTER + PAGINATION
========================= */

function filterMovies() {


    const searchValue =
        movieSearch.value
        .toLowerCase()
        .trim();


    const selectedGenre =
        genreFilter.value
        .toLowerCase()
        .trim();


    const matchingMovies =
        movieCards.filter(
            function(card) {


                const title =
                    card.dataset.title
                    .toLowerCase()
                    .trim();


                /*
                    A movie can have multiple genres.

                    Example:
                    "Action, Sci-Fi, Thriller"
                */

                const genres =
                    card.dataset.genre
                    .toLowerCase()
                    .split(",")
                    .map(
                        function(genre) {

                            return genre.trim();

                        }
                    );


                const matchesSearch =
                    title.includes(
                        searchValue
                    );


                const matchesGenre =
                    selectedGenre === "all" ||
                    genres.some(
                        function(genre) {

                            return (
                                genre === selectedGenre ||
                                genre.includes(
                                    selectedGenre
                                ) ||
                                selectedGenre.includes(
                                    genre
                                )
                            );

                        }
                    );


                return (
                    matchesSearch &&
                    matchesGenre
                );

            }
        );


    /*
        Start from page 1
        whenever the filter changes.
    */

    currentPage = 1;


    displayMovies(
        matchingMovies
    );

}



/* =========================
   DISPLAY MOVIES
========================= */

function displayMovies(
    matchingMovies
) {


    /*
        Hide every movie first.
    */

    movieCards.forEach(
        function(card) {

            card.style.display =
                "none";

        }
    );


    /*
        Show no-results message
        when nothing matches.
    */

    if (
        matchingMovies.length === 0
    ) {

        noResults.style.display =
            "block";

        moviePagination.innerHTML =
            "";

        return;

    }


    noResults.style.display =
        "none";


    /*
        Calculate total pages.
    */

    const totalPages =
        Math.ceil(
            matchingMovies.length /
            moviesPerPage
        );


    /*
        Make sure current page
        always exists.
    */

    if (
        currentPage > totalPages
    ) {

        currentPage =
            totalPages;

    }


    /*
        Calculate which movies
        belong to this page.
    */

    const startIndex =
        (
            currentPage - 1
        ) *
        moviesPerPage;


    const endIndex =
        startIndex +
        moviesPerPage;


    const moviesToShow =
        matchingMovies.slice(
            startIndex,
            endIndex
        );


    /*
        Display current page movies.
    */

    moviesToShow.forEach(
        function(card) {

            card.style.display =
                "";

        }
    );


    /*
        Create pagination.
    */

    createPagination(
        totalPages
    );

}



/* =========================
   CREATE PAGINATION
========================= */

function createPagination(
    totalPages
) {


    moviePagination.innerHTML =
        "";


    /*
        No pagination needed
        for a single page.
    */

    if (
        totalPages <= 1
    ) {

        return;

    }


    /*
        PREVIOUS BUTTON
    */

    if (
        currentPage > 1
    ) {

        const previous =
            document.createElement(
                "button"
            );


        previous.type =
            "button";


        previous.textContent =
            "←";


        previous.className =
            "user-page-btn";


        previous.addEventListener(
            "click",
            function() {

                currentPage--;

                showCurrentFilter();

            }
        );


        moviePagination.appendChild(
            previous
        );

    }


    /*
        PAGE NUMBERS
    */

    for (
        let page = 1;
        page <= totalPages;
        page++
    ) {


        const pageButton =
            document.createElement(
                "button"
            );


        pageButton.type =
            "button";


        pageButton.textContent =
            page;


        pageButton.className =
            "user-page-btn";


        if (
            page === currentPage
        ) {

            pageButton.classList.add(
                "active"
            );

        }


        pageButton.addEventListener(
            "click",
            function() {

                currentPage =
                    page;

                showCurrentFilter();

            }
        );


        moviePagination.appendChild(
            pageButton
        );

    }


    /*
        NEXT BUTTON
    */

    if (
        currentPage < totalPages
    ) {

        const next =
            document.createElement(
                "button"
            );


        next.type =
            "button";


        next.textContent =
            "→";


        next.className =
            "user-page-btn";


        next.addEventListener(
            "click",
            function() {

                currentPage++;

                showCurrentFilter();

            }
        );


        moviePagination.appendChild(
            next
        );

    }

}



/* =========================
   SHOW CURRENT FILTER
========================= */

function showCurrentFilter() {


    const searchValue =
        movieSearch.value
        .toLowerCase()
        .trim();


    const selectedGenre =
        genreFilter.value
        .toLowerCase()
        .trim();


    const matchingMovies =
        movieCards.filter(
            function(card) {


                const title =
                    card.dataset.title
                    .toLowerCase()
                    .trim();


                const genres =
                    card.dataset.genre
                    .toLowerCase()
                    .split(",")
                    .map(
                        function(genre) {

                            return genre.trim();

                        }
                    );


                const matchesSearch =
                    title.includes(
                        searchValue
                    );


                const matchesGenre =
                    selectedGenre === "all" ||
                    genres.some(
                        function(genre) {

                            return (
                                genre === selectedGenre ||
                                genre.includes(
                                    selectedGenre
                                ) ||
                                selectedGenre.includes(
                                    genre
                                )
                            );

                        }
                    );


                return (
                    matchesSearch &&
                    matchesGenre
                );

            }
        );


    displayMovies(
        matchingMovies
    );

}



/* =========================
   SEARCH BUTTON
========================= */

searchButton.addEventListener(
    "click",
    function() {

        currentPage = 1;

        filterMovies();

    }
);



/* =========================
   LIVE SEARCH
========================= */

movieSearch.addEventListener(
    "input",
    function() {

        currentPage = 1;

        filterMovies();

    }
);



/* =========================
   GENRE FILTER
========================= */

genreFilter.addEventListener(
    "change",
    function() {

        currentPage = 1;

        filterMovies();

    }
);



/* =========================
   INITIAL DISPLAY
========================= */

displayMovies(
    movieCards
);

</script>


</body>

</html>