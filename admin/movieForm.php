<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/adminAuth.php';

$movieID = (int) ($_GET["id"] ?? $_POST["movieID"] ?? 0);


$movie = [
    "title" => "",
    "poster" => "images/movies/",
    "releaseDate" => "",
    "genre" => "",
    "language" => "",
    "runtime" => "",
    "description" => "",
];


/*
|--------------------------------------------------------------------------
| Load movie
|--------------------------------------------------------------------------
*/

if ($movieID > 0) {

    $stmt = $conn->prepare(
        "SELECT * FROM movie WHERE movieID = ?"
    );

    $stmt->bind_param("i", $movieID);
    $stmt->execute();

    $found =
        $stmt->get_result()->fetch_assoc();

    if (!$found) {

        $_SESSION["flash"] =
            "Movie not found.";

        header("Location: movies.php");
        exit;
    }

    $movie = $found;
}


$error = "";


/*
|--------------------------------------------------------------------------
| Save movie
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();


    foreach (
        [
            "title",
            "poster",
            "releaseDate",
            "genre",
            "language",
            "runtime",
            "description"
        ]
        as $field
    ) {

        $movie[$field] =
            trim($_POST[$field] ?? "");
    }


    /*
    |--------------------------------------------------------------------------
    | Validate poster
    |--------------------------------------------------------------------------
    */

    $posterOk =
        preg_match(
            '#^images/[A-Za-z0-9_\-./ ]+\.(jpg|jpeg|png|webp)$#i',
            $movie["poster"]
        )
        &&
        strpos(
            $movie["poster"],
            ".."
        ) === false;


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

  if ($movie["title"] === "") {
    $error = "Title is required.";
} elseif (
    $movie["releaseDate"] !== "" &&
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $movie["releaseDate"])
) {
    $error = "Please enter a valid release date.";
} elseif (!$posterOk) {

        $error =
            "Poster must be an image path like images/movies/Inception.jpg.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        if ($movieID > 0) {

            $stmt = $conn->prepare(
                "UPDATE movie
                 SET title = ?,
                     poster = ?,
                     releaseDate = ?,
                     genre = ?,
                     language = ?,
                     runtime = ?,
                     description = ?
                 WHERE movieID = ?"
            );

            $stmt->bind_param(
                "sssssssi",
                $movie["title"],
                $movie["poster"],
                $movie["releaseDate"],
                $movie["genre"],
                $movie["language"],
                $movie["runtime"],
                $movie["description"],
                $movieID
            );

            $stmt->execute();

            $_SESSION["flash"] =
                "Movie updated successfully.";


        /*
        |--------------------------------------------------------------------------
        | Insert
        |--------------------------------------------------------------------------
        */

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO movie
                (
                    title,
                    poster,
                    releaseDate,
                    genre,
                    language,
                    runtime,
                    description
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssss",
                $movie["title"],
                $movie["poster"],
                $movie["releaseDate"],
                $movie["genre"],
                $movie["language"],
                $movie["runtime"],
                $movie["description"]
            );

            $stmt->execute();

            $_SESSION["flash"] =
                "Movie added successfully.";
        }


        header("Location: movies.php");
        exit;
    }
}


$pageTitle =
    $movieID > 0
        ? "Edit Movie"
        : "Add Movie";

include 'adminHeader.php';
?>


<div class="page-heading">

    <div>

        <p class="admin-eyebrow">
            MOVIE LIBRARY
        </p>

        <h1>
            <?php echo e($pageTitle); ?>
        </h1>

        <p class="page-subtitle">
            <?php
            echo $movieID > 0
                ? "Update the movie information."
                : "Add a new movie to Movie Media.";
            ?>
        </p>

    </div>

</div>


<?php if ($error !== ""): ?>

    <p class="flash error">
        <?php echo e($error); ?>
    </p>

<?php endif; ?>


<form
    method="post"
    class="admin-form movie-form"
>

    <?php echo csrfField(); ?>


    <input
        type="hidden"
        name="movieID"
        value="<?php echo (int) $movieID; ?>"
    >


    <div class="form-section">

        <h2>Basic Information</h2>

        <label>

            Title

            <input
                type="text"
                name="title"
                required
                value="<?php echo e($movie["title"]); ?>"
                placeholder="Movie title"
            >

        </label>


        <label>

            Description

            <textarea
                name="description"
                placeholder="Write a short description of the movie..."
            ><?php echo e($movie["description"]); ?></textarea>

        </label>

    </div>


    <div class="form-section">

        <h2>Movie Details</h2>

        <div class="form-grid">

          <label>
    Release date
    <input
        type="date"
        name="releaseDate"
        value="<?php echo e($movie["releaseDate"]); ?>"
    >
    <small class="form-help">
        Select the movie's release date.
    </small>
</label>


            <label>

                Genre

                <input
                    type="text"
                    name="genre"
                    value="<?php echo e($movie["genre"]); ?>"
                    placeholder="Drama, Thriller"
                >

            </label>


            <label>

                Language

                <input
                    type="text"
                    name="language"
                    value="<?php echo e($movie["language"]); ?>"
                    placeholder="English"
                >

            </label>


            <label>

                Runtime

                <input
                    type="text"
                    name="runtime"
                    value="<?php echo e($movie["runtime"]); ?>"
                    placeholder="120"
                >

            </label>

        </div>

    </div>


    <div class="form-section">

        <h2>Poster</h2>

        <label>

            Poster path

            <input
                type="text"
                name="poster"
                required
                value="<?php echo e($movie["poster"]); ?>"
                placeholder="images/movies/MovieName.jpg"
            >

        </label>

        <p class="form-help">
            Use an image stored inside the Movie Media images/movies folder.
        </p>

    </div>


    <div class="form-actions">

        <button type="submit">
            <?php
            echo $movieID > 0
                ? "Save Changes"
                : "Add Movie";
            ?>
        </button>

        <a
            class="btn secondary-btn"
            href="movies.php"
        >
            Cancel
        </a>

    </div>

</form>


<?php include 'adminFooter.php'; ?>