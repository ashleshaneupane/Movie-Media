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
                    data-title="<?php echo htmlspecialchars($movie['title']); ?>"
                    data-genre="<?php echo htmlspecialchars($movie['genre']); ?>"
                >


                    <a
                        href="movieDetails.php?movie=<?php echo $movie['movieID']; ?>"
                    >

                        <div class="movie-poster">

                            <img
                                src="<?php echo htmlspecialchars($movie['poster']); ?>"
                                alt="<?php echo htmlspecialchars($movie['title']); ?>"
                            >

                        </div>

                    </a>


                    <div class="movie-card-info">


                        <h3>

                            <?php echo htmlspecialchars($movie['title']); ?>

                        </h3>


                        <p>

                            <?php echo htmlspecialchars($movie['genre']); ?>

                            •

                            <?php echo htmlspecialchars($movie['releaseDate']); ?>

                        </p>


                        <!-- MOVIE RATING -->

                        <span>

                            <?php if ($movie['averageRating'] === null): ?>

                                ⭐ No rating yet

                            <?php else: ?>

                                ⭐
                                <?php echo number_format((float) $movie['averageRating'], 1); ?>
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



/*
    FILTER MOVIES
*/

function filterMovies() {


    const searchValue =
        movieSearch.value
        .toLowerCase()
        .trim();


    const selectedGenre =
        genreFilter.value
        .toLowerCase()
        .trim();


    let foundMovies = 0;



    movieCards.forEach(function(card) {


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
            .map(function(genre) {
                return genre.trim();
            });



        const matchesSearch =
            title.includes(searchValue);



        const matchesGenre =
            selectedGenre === "all" ||
            genres.some(function(genre) {

                return (
                    genre === selectedGenre ||
                    genre.includes(selectedGenre) ||
                    selectedGenre.includes(genre)
                );

            });



        if (
            matchesSearch &&
            matchesGenre
        ) {

            card.style.display = "";

            foundMovies++;

        }
        else {

            card.style.display = "none";

        }

    });



    if (foundMovies === 0) {

        noResults.style.display = "block";

    }
    else {

        noResults.style.display = "none";

    }

}



/*
    SEARCH BUTTON
*/

searchButton.addEventListener(
    "click",
    filterMovies
);



/*
    LIVE SEARCH
*/

movieSearch.addEventListener(
    "input",
    filterMovies
);



/*
    GENRE FILTER
*/

genreFilter.addEventListener(
    "change",
    filterMovies
);


</script>


</body>

</html>