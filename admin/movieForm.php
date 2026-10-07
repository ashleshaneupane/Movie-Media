<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/adminAuth.php';


$movieID = (int) ($_GET["id"] ?? $_POST["movieID"] ?? 0);


/*
|--------------------------------------------------------------------------
| Default movie data
|--------------------------------------------------------------------------
*/

$movie = [
    "title" => "",
    "poster" => "",
    "releaseDate" => "",
    "genre" => "",
    "language" => "",
    "runtime" => "",
    "description" => "",
];


/*
|--------------------------------------------------------------------------
| Load existing movie
|--------------------------------------------------------------------------
*/

if ($movieID > 0) {

    $stmt = $conn->prepare(
        "SELECT * FROM movie WHERE movieID = ?"
    );

    $stmt->bind_param(
        "i",
        $movieID
    );

    $stmt->execute();

    $found = $stmt
        ->get_result()
        ->fetch_assoc();


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


    /*
    |--------------------------------------------------------------------------
    | Get form values
    |--------------------------------------------------------------------------
    */

    $movie["title"] =
        trim($_POST["title"] ?? "");


    $movie["releaseDate"] =
        trim($_POST["releaseDate"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Multiple genres
    |--------------------------------------------------------------------------
    */

    $selectedGenres =
        $_POST["genre"] ?? [];


    if (!is_array($selectedGenres)) {

        $selectedGenres = [];
    }


    $selectedGenres = array_map(
        "trim",
        $selectedGenres
    );


    $selectedGenres = array_filter(
        $selectedGenres,
        function ($genre) {
            return $genre !== "";
        }
    );


    $movie["genre"] =
        implode(", ", $selectedGenres);


    $movie["language"] =
        trim($_POST["language"] ?? "");


    $movie["runtime"] =
        trim($_POST["runtime"] ?? "");


    $movie["description"] =
        trim($_POST["description"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Validate basic information
    |--------------------------------------------------------------------------
    */

    if ($movie["title"] === "") {

        $error =
            "Title is required.";

    } elseif (
        $movie["releaseDate"] !== "" &&
        !preg_match(
            '/^\d{4}-\d{2}-\d{2}$/',
            $movie["releaseDate"]
        )
    ) {

        $error =
            "Please enter a valid release date.";
    }


    /*
    |--------------------------------------------------------------------------
    | Handle poster upload
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        /*
        |--------------------------------------------------------------------------
        | A new poster was uploaded
        |--------------------------------------------------------------------------
        */

        if (
            isset($_FILES["posterFile"]) &&
            $_FILES["posterFile"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if (
                $_FILES["posterFile"]["error"] !== UPLOAD_ERR_OK
            ) {

                $error =
                    "There was a problem uploading the poster.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | Check file type
                |--------------------------------------------------------------------------
                */

                $allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];


                $fileType =
                    mime_content_type(
                        $_FILES["posterFile"]["tmp_name"]
                    );


                if (
                    !in_array(
                        $fileType,
                        $allowedTypes,
                        true
                    )
                ) {

                    $error =
                        "Poster must be a JPG, JPEG, PNG, or WEBP image.";

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Check file extension
                    |--------------------------------------------------------------------------
                    */

                    $extension =
                        strtolower(
                            pathinfo(
                                $_FILES["posterFile"]["name"],
                                PATHINFO_EXTENSION
                            )
                        );


                    $allowedExtensions = [
                        "jpg",
                        "jpeg",
                        "png",
                        "webp"
                    ];


                    if (
                        !in_array(
                            $extension,
                            $allowedExtensions,
                            true
                        )
                    ) {

                        $error =
                            "Invalid poster file type.";

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Movie poster folder
                        |--------------------------------------------------------------------------
                        */
$uploadDirectory = dirname(__DIR__) . "/images/movies/";


                        /*
                        |--------------------------------------------------------------------------
                        | Make sure folder exists
                        |--------------------------------------------------------------------------
                        */

                      if (!is_dir($uploadDirectory)) {
    $error = "The images/movies folder does not exist.";
} else {

                            /*
                            |--------------------------------------------------------------------------
                            | Generate unique filename
                            |--------------------------------------------------------------------------
                            */

                            $fileName =
                                uniqid(
                                    "movie_",
                                    true
                                )
                                . "."
                                . $extension;


                            $uploadPath =
                                $uploadDirectory
                                . $fileName;


                            /*
                            |--------------------------------------------------------------------------
                            | Move uploaded poster
                            |--------------------------------------------------------------------------
                            */

                            if (
                                move_uploaded_file(
                                    $_FILES["posterFile"]["tmp_name"],
                                    $uploadPath
                                )
                            ) {

                                $movie["poster"] =
                                    "images/movies/"
                                    . $fileName;

                            } else {

                                $error =
                                    "Could not save the uploaded poster.";
                            }
                        }
                    }
                }
            }


        /*
        |--------------------------------------------------------------------------
        | No new poster uploaded
        |--------------------------------------------------------------------------
        */

        } elseif ($movieID === 0) {

            /*
             * Adding a new movie requires a poster.
             */

            $error =
                "Please upload a poster.";


        } elseif ($movieID > 0) {

            /*
             * Editing an existing movie without
             * uploading a new poster keeps the
             * existing poster.
             */

            $movie["poster"] =
                $found["poster"];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Save to database
    |--------------------------------------------------------------------------
    */

    if ($error === "") {


        /*
        |--------------------------------------------------------------------------
        | Update existing movie
        |--------------------------------------------------------------------------
        */

        if ($movieID > 0) {

            $stmt = $conn->prepare(
                "UPDATE movie
                 SET
                    title = ?,
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
        | Add new movie
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


        /*
        |--------------------------------------------------------------------------
        | Redirect back to movie list
        |--------------------------------------------------------------------------
        */

        header(
            "Location: movies.php"
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Page title
|--------------------------------------------------------------------------
*/

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
    enctype="multipart/form-data"
    class="admin-form movie-form"
>


    <?php echo csrfField(); ?>


    <input
        type="hidden"
        name="movieID"
        value="<?php echo (int) $movieID; ?>"
    >


    <!--
    |--------------------------------------------------------------------------
    | Basic Information
    |--------------------------------------------------------------------------
    -->


    <div class="form-section">

        <h2>
            Basic Information
        </h2>


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


    <!--
    |--------------------------------------------------------------------------
    | Movie Details
    |--------------------------------------------------------------------------
    -->


    <div class="form-section">

        <h2>
            Movie Details
        </h2>


        <div class="form-grid">


            <!-- Release Date -->


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


            <!-- Genre -->


            <label>

                Genre


                <?php

                $genres = [
                    "Action",
                    "Adventure",
                    "Animation",
                    "Comedy",
                    "Crime",
                    "Documentary",
                    "Drama",
                    "Fantasy",
                    "Horror",
                    "Mystery",
                    "Romance",
                    "Sci-Fi",
                    "Thriller"
                ];


                $selectedGenres = array_map(
                    "trim",
                    explode(
                        ",",
                        $movie["genre"] ?? ""
                    )
                );

                ?>


                <select
                    name="genre[]"
                    multiple
                    size="6"
                >


                    <?php foreach ($genres as $genre): ?>


                        <option
                            value="<?php echo e($genre); ?>"
                            <?php

                            echo in_array(
                                $genre,
                                $selectedGenres,
                                true
                            )
                                ? "selected"
                                : "";

                            ?>
                        >

                            <?php echo e($genre); ?>

                        </option>


                    <?php endforeach; ?>


                </select>


                <small class="form-help">

                    Hold Command and select multiple genres.

                </small>

            </label>


            <!-- Language -->


            <label>

                Language

                <input
                    type="text"
                    name="language"
                    value="<?php echo e($movie["language"]); ?>"
                    placeholder="English"
                >

            </label>


            <!-- Runtime -->


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


    <!--
    |--------------------------------------------------------------------------
    | Poster
    |--------------------------------------------------------------------------
    -->


    <div class="form-section">

        <h2>
            Poster
        </h2>


        <label>

            Upload poster

            <input
                type="file"
                name="posterFile"
                accept=".jpg,.jpeg,.png,.webp"
                <?php echo $movieID > 0 ? "" : "required"; ?>
            >

        </label>


        <p class="form-help">

            Upload a JPG, JPEG, PNG, or WEBP image.

        </p>


        <?php if (
            $movieID > 0 &&
            !empty($movie["poster"])
        ): ?>


            <p class="form-help">

                Current poster:

                <?php echo e($movie["poster"]); ?>

            </p>


        <?php endif; ?>


    </div>


    <!--
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    -->


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