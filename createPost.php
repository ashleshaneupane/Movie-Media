
<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = (int) $_SESSION["userID"];
$error = "";

/* =========================
   GET MOVIES
========================= */

$movieQuery = $conn->query(
    "SELECT movieID, title
     FROM movie
     ORDER BY title ASC"
);

$allMovies = [];

while ($row = $movieQuery->fetch_assoc()) {
    $allMovies[] = $row;
}

/* =========================
   DEFAULT VALUES
========================= */

$caption = "";
$selectedMovies = [];
$watchedDate = "";
$imageURL = null;

/* =========================
   CREATE POST
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $caption = trim($_POST["postContent"] ?? "");

    /* GET MULTIPLE MOVIES */

    if (
        isset($_POST["movieIDs"]) &&
        is_array($_POST["movieIDs"])
    ) {
        foreach ($_POST["movieIDs"] as $id) {
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id !== false && $id > 0) {
                $selectedMovies[] = (int) $id;
            }
        }
    }

    $selectedMovies = array_values(
        array_unique($selectedMovies)
    );

    /* WATCHED DATE */

    $watchedDate = !empty($_POST["watchedDate"])
        ? trim($_POST["watchedDate"])
        : null;

    /* EXISTING IMAGE VALUE */

    $imageURL = null;

    /* =========================
       PHOTO UPLOAD
    ========================= */

    if (
        isset($_FILES["photo"]) &&
        $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {

            $error = "There was a problem uploading the image.";

        } else {

            $allowedTypes = [
                "image/jpeg" => "jpg",
                "image/png"  => "png",
                "image/gif"  => "gif",
                "image/webp" => "webp"
            ];

            $fileType = mime_content_type(
                $_FILES["photo"]["tmp_name"]
            );

            if (!isset($allowedTypes[$fileType])) {

                $error = "Please upload a valid JPG, PNG, GIF, or WEBP image.";

            } else {

                $uploadDirectory = __DIR__ . "/images/posts/";

                if (
                    !is_dir($uploadDirectory) &&
                    !mkdir($uploadDirectory, 0775, true) &&
                    !is_dir($uploadDirectory)
                ) {
                    $error = "Could not create the image upload folder.";
                } else {

                    $fileName = uniqid("post_", true)
                        . "."
                        . $allowedTypes[$fileType];

                    $filePath = $uploadDirectory . $fileName;

                    if (
                        move_uploaded_file(
                            $_FILES["photo"]["tmp_name"],
                            $filePath
                        )
                    ) {
                        $imageURL = "images/posts/" . $fileName;
                    } else {
                        $error = "Could not save the uploaded image.";
                    }
                }
            }
        }
    }

    /* =========================
       VALIDATION
    ========================= */

    if (
        $error === "" &&
        $caption === "" &&
        empty($selectedMovies) &&
        $imageURL === null
    ) {
        $error = "Please write something or add a movie/photo.";
    }

    /* VALIDATE WATCHED DATE */

    if ($error === "" && $watchedDate !== null) {

        $dateCheck = DateTime::createFromFormat(
            "!Y-m-d",
            $watchedDate
        );

        if (
            !$dateCheck ||
            $dateCheck->format("Y-m-d") !== $watchedDate
        ) {
            $error = "Please enter a valid watched date.";
        }
    }

    /* =========================
       VALIDATE ALL MOVIES
    ========================= */

    if ($error === "" && !empty($selectedMovies)) {

        $placeholders = implode(
            ",",
            array_fill(0, count($selectedMovies), "?")
        );

        $types = str_repeat("i", count($selectedMovies));

        $movieCheck = $conn->prepare(
            "SELECT movieID
             FROM movie
             WHERE movieID IN ($placeholders)"
        );

        if (!$movieCheck) {
            $error = "Could not validate the selected movies.";
        } else {

            $movieCheck->bind_param(
                $types,
                ...$selectedMovies
            );

            $movieCheck->execute();

            $movieResult = $movieCheck->get_result();

            $validMovieIDs = [];

            while ($row = $movieResult->fetch_assoc()) {
                $validMovieIDs[] = (int) $row["movieID"];
            }

            sort($validMovieIDs);

            $expectedMovieIDs = $selectedMovies;
            sort($expectedMovieIDs);

            if ($validMovieIDs !== $expectedMovieIDs) {
                $error = "One or more selected movies do not exist.";
            }
        }
    }

    /* =========================
       SAVE POST + MOVIES
    ========================= */

    if ($error === "") {

        $conn->begin_transaction();

        try {

            /*
             * One post can have multiple movies.
             * PostMovie stores the movie relationships.
             */

            $postQuery = $conn->prepare(
                "INSERT INTO Post
                    (userID, movieID, caption, imageURL, watchedDate)
                 VALUES (?, NULL, ?, ?, ?)"
            );

            if (!$postQuery) {
                throw new Exception("Could not prepare post.");
            }

            $postQuery->bind_param(
                "isss",
                $userID,
                $caption,
                $imageURL,
                $watchedDate
            );

            if (!$postQuery->execute()) {
                throw new Exception("Could not insert post.");
            }

            $newPostID = (int) $conn->insert_id;

            /* ATTACH EVERY SELECTED MOVIE */

            if (!empty($selectedMovies)) {

                $movieInsert = $conn->prepare(
                    "INSERT INTO PostMovie (postID, movieID)
                     VALUES (?, ?)"
                );

                if (!$movieInsert) {
                    throw new Exception("Could not prepare movie links.");
                }

                foreach ($selectedMovies as $selectedMovieID) {

                    $movieInsert->bind_param(
                        "ii",
                        $newPostID,
                        $selectedMovieID
                    );

                    if (!$movieInsert->execute()) {
                        throw new Exception("Could not attach a movie.");
                    }
                }
            }

            $conn->commit();

            header("Location: home.php?post=success");
            exit;

        } catch (Throwable $exception) {

            $conn->rollback();

            $error = "Could not create the post. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Post - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

    <style>
        .user-movie-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            max-height: 280px;
            overflow-y: auto;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 12px;
            background: #151515;
            margin-top: 10px;
        }

        .user-movie-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border: 1px solid #303030;
            border-radius: 9px;
            background: #202020;
            color: #f2f2f2;
            cursor: pointer;
        }

        .user-movie-option:hover {
            background: #2a2a2a;
        }

        .user-movie-option input[type="checkbox"] {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            accent-color: #d4a84f;
            cursor: pointer;
        }

        .movie-selection-help {
            color: #aaa;
            font-size: 13px;
            margin: 8px 0;
        }
    </style>

</head>

<body>

<?php include 'includes/header.php'; ?>

<main class="create-post-container">

    <div class="create-post-header">

        <p class="create-post-label">
            SHARE YOUR MOVIE EXPERIENCE
        </p>

        <h1 id="page-title">Create Post</h1>

        <p class="create-post-description">
            Share your thoughts, add movies, or tell others what you watched.
        </p>

    </div>

    <?php if ($error !== ""): ?>

        <div class="create-post-error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>

    <form
        class="create-post-form"
        id="createPostForm"
        method="POST"
        enctype="multipart/form-data"
    >

        <textarea
            id="postContent"
            name="postContent"
            placeholder="What's on your mind?"
        ><?php echo htmlspecialchars($caption); ?></textarea>

        <!-- POST OPTIONS -->

        <div class="post-options">

            <button
                type="button"
                class="post-option"
                id="addMovieBtn"
            >
                🎬 <span>Add Movies</span>
            </button>

            <button
                type="button"
                class="post-option"
                id="addPhotoBtn"
            >
                📷 <span>Add Photo</span>
            </button>

            <label for="watchedDate" class="post-option">

                📅 <span>Watched Date</span>

                <input
                    type="date"
                    id="watchedDate"
                    name="watchedDate"
                    value="<?php echo htmlspecialchars($watchedDate ?? ""); ?>"
                >

            </label>

        </div>

        <!-- MULTIPLE MOVIE SELECTION -->

        <div class="movie-selection" id="movieSelection">

            <p><strong>Select Movies</strong></p>

            <p class="movie-selection-help">
                Choose as many movies as you want for this post.
            </p>

            <div class="user-movie-options">

                <?php foreach ($allMovies as $movie): ?>

                    <label class="user-movie-option">

                        <input
                            type="checkbox"
                            name="movieIDs[]"
                            value="<?php echo (int) $movie["movieID"]; ?>"
                            <?php
                            echo in_array(
                                (int) $movie["movieID"],
                                $selectedMovies,
                                true
                            ) ? "checked" : "";
                            ?>
                        >

                        <span>
                            <?php echo htmlspecialchars($movie["title"]); ?>
                        </span>

                    </label>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- PHOTO UPLOAD -->

        <div class="photo-selection" id="photoSelection">

            <label for="photoInput">Choose a photo</label>

            <input
                type="file"
                id="photoInput"
                name="photo"
                accept="image/jpeg,image/png,image/gif,image/webp"
            >

        </div>

        <!-- POST BUTTON -->

        <div class="post-submit">

            <button type="submit">Post</button>

        </div>

    </form>

</main>

<script>
const addMovieBtn = document.getElementById("addMovieBtn");
const addPhotoBtn = document.getElementById("addPhotoBtn");
const movieSelection = document.getElementById("movieSelection");
const photoSelection = document.getElementById("photoSelection");

addMovieBtn.addEventListener("click", function () {
    movieSelection.classList.toggle("active");
});

addPhotoBtn.addEventListener("click", function () {
    photoSelection.classList.toggle("active");
});
</script>

</body>
</html>