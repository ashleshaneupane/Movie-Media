<?php
include '../includes/adminAuth.php';


/*
|--------------------------------------------------------------------------
| Delete movie
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    $movieID = (int) ($_POST["movieID"] ?? 0);

    if ($movieID > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM movie WHERE movieID = ?"
        );

        $stmt->bind_param("i", $movieID);
        $stmt->execute();

        $_SESSION["flash"] = "Movie deleted.";
    }

    header("Location: movies.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Search and filters
|--------------------------------------------------------------------------
*/

$search = trim($_GET["q"] ?? "");
$genreFilter = trim($_GET["genre"] ?? "");
$yearFilter = trim($_GET["year"] ?? "");

$conditions = [];
$params = [];
$types = "";


/* Search */

if ($search !== "") {

    $conditions[] =
        "(title LIKE ? OR genre LIKE ? OR description LIKE ?)";

    $like = "%" . $search . "%";

    $params[] = $like;
    $params[] = $like;
    $params[] = $like;

    $types .= "sss";
}


/* Genre */

if ($genreFilter !== "") {

    $conditions[] = "genre LIKE ?";

    $params[] = "%" . $genreFilter . "%";
    $types .= "s";
}


/* Year */

if ($yearFilter !== "") {

    $conditions[] =
        "YEAR(releaseDate) = ?";

    $params[] = (int) $yearFilter;
    $types .= "i";
}


/*
|--------------------------------------------------------------------------
| Build movie query
|--------------------------------------------------------------------------
*/

$sql =
    "SELECT movieID, title, poster, releaseDate,
            genre, language, runtime, description
     FROM movie";

if (!empty($conditions)) {

    $sql .=
        " WHERE " .
        implode(" AND ", $conditions);
}

$sql .= " ORDER BY title ASC";


$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$movies = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Get genres for filter
|--------------------------------------------------------------------------
*/

$genreResult = $conn->query(
    "SELECT DISTINCT genre
     FROM movie
     WHERE genre IS NOT NULL
     AND genre != ''
     ORDER BY genre ASC"
);

$genres = [];

while ($g = $genreResult->fetch_assoc()) {

    $parts = explode(",", $g["genre"]);

    foreach ($parts as $part) {

        $part = trim($part);

        if ($part !== "") {
            $genres[$part] = true;
        }
    }
}

$genres = array_keys($genres);
sort($genres);


/*
|--------------------------------------------------------------------------
| Get years for filter
|--------------------------------------------------------------------------
*/

$yearResult = $conn->query(
    "SELECT DISTINCT YEAR(releaseDate) AS movieYear
     FROM movie
     WHERE releaseDate IS NOT NULL
     AND releaseDate != ''
     ORDER BY movieYear DESC"
);

$years = [];

while ($y = $yearResult->fetch_assoc()) {

    if (!empty($y["movieYear"])) {
        $years[] = $y["movieYear"];
    }
}


$pageTitle = "Movies";
include 'adminHeader.php';
?>


<div class="page-heading">

    <div>

        <p class="admin-eyebrow">
            MOVIE LIBRARY
        </p>

        <h1>Movies</h1>

        <p class="page-subtitle">
            Manage the movies available throughout Movie Media.
        </p>

    </div>

</div>


<!-- Search and filters -->

<form class="movie-toolbar" method="get">

    <input
        type="search"
        name="q"
        value="<?php echo e($search); ?>"
        placeholder="Search title, genre or description"
    >


    <select name="genre">

        <option value="">
            All genres
        </option>

        <?php foreach ($genres as $genre): ?>

            <option
                value="<?php echo e($genre); ?>"
                <?php echo $genreFilter === $genre ? "selected" : ""; ?>
            >
                <?php echo e($genre); ?>
            </option>

        <?php endforeach; ?>

    </select>


    <select name="year">

        <option value="">
            All years
        </option>

        <?php foreach ($years as $year): ?>

            <option
                value="<?php echo (int) $year; ?>"
                <?php echo $yearFilter == $year ? "selected" : ""; ?>
            >
                <?php echo (int) $year; ?>
            </option>

        <?php endforeach; ?>

    </select>


    <button type="submit">
        Search
    </button>


    <?php if (
        $search !== "" ||
        $genreFilter !== "" ||
        $yearFilter !== ""
    ): ?>

        <a href="movies.php" class="clear-filter">
            Clear
        </a>

    <?php endif; ?>

</form>


<!-- Add movie -->

<div class="movie-actions">

    <a
        class="btn"
        href="movieForm.php"
    >
        + Add Movie
    </a>

</div>


<!-- Movies -->

<div class="movie-grid">

    <?php if ($movies->num_rows === 0): ?>

        <div class="empty-state movie-empty">

            <strong>No movies found.</strong>

            <span>
                Try changing your search or filters.
            </span>

        </div>

    <?php else: ?>

        <?php while ($m = $movies->fetch_assoc()): ?>

            <article class="movie-card">


                <!-- Poster -->

                <div class="movie-poster">

                    <?php if (!empty($m["poster"])): ?>

                        <img
                            src="../<?php echo e($m["poster"]); ?>"
                            alt="<?php echo e($m["title"]); ?>"
                        >

                    <?php else: ?>

                        <div class="poster-placeholder">
                            No Poster
                        </div>

                    <?php endif; ?>

                </div>


                <!-- Information -->

                <div class="movie-info">

                    <h2>
                        <?php echo e($m["title"]); ?>
                    </h2>


                    <div class="movie-meta">

                        <?php if (!empty($m["releaseDate"])): ?>

                            <span>
                                <?php
                                echo e(
                                    date(
                                        "Y",
                                        strtotime($m["releaseDate"])
                                    )
                                );
                                ?>
                            </span>

                        <?php endif; ?>


                        <?php if (!empty($m["language"])): ?>

                            <span>
                                <?php echo e($m["language"]); ?>
                            </span>

                        <?php endif; ?>


                        <?php if (!empty($m["runtime"])): ?>

                            <span>
                                <?php echo e($m["runtime"]); ?> min
                            </span>

                        <?php endif; ?>

                    </div>


                    <?php if (!empty($m["genre"])): ?>

                        <p class="movie-genre">
                            <?php echo e($m["genre"]); ?>
                        </p>

                    <?php endif; ?>


                    <?php if (!empty($m["description"])): ?>

                        <p class="movie-description">

                            <?php
                            $description =
                                trim($m["description"]);

                            if (strlen($description) > 150) {
                                $description =
                                    substr(
                                        $description,
                                        0,
                                        150
                                    ) . "...";
                            }

                            echo e($description);
                            ?>

                        </p>

                    <?php else: ?>

                        <p class="movie-description empty-description">
                            No description added.
                        </p>

                    <?php endif; ?>


                    <!-- Actions -->

                    <div class="movie-card-actions">

                        <a
                            class="btn"
                            href="movieForm.php?id=<?php echo (int) $m["movieID"]; ?>"
                        >
                            Edit
                        </a>


                        <form
                            method="post"
                            class="inline"
                            onsubmit="return confirm('Delete this movie permanently?');"
                        >

                            <?php echo csrfField(); ?>

                            <input
                                type="hidden"
                                name="movieID"
                                value="<?php echo (int) $m["movieID"]; ?>"
                            >

                            <button
                                type="submit"
                                class="danger"
                            >
                                Delete
                            </button>

                        </form>

                    </div>

                </div>

            </article>

        <?php endwhile; ?>

    <?php endif; ?>

</div>


<?php include 'adminFooter.php'; ?>