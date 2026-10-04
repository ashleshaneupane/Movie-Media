<?php

include 'includes/auth.php';
include 'includes/config.php';


$userID = $_SESSION["userID"];

$error = "";


/* =========================
   GET MOVIES
========================= */

$movieQuery = $conn->query(
    "SELECT movieID, title
     FROM movie
     ORDER BY title ASC"
);


/* =========================
   CREATE POST
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $caption = trim(
        $_POST["postContent"] ?? ""
    );


    $movieID = !empty($_POST["movieID"])
        ? (int) $_POST["movieID"]
        : null;


    $watchedDate = !empty($_POST["watchedDate"])
        ? $_POST["watchedDate"]
        : null;


    /* =========================
       CHECK PHOTO
    ========================= */

    $imageURL = null;


    if (
        isset($_FILES["photo"]) &&
        $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {


        if (
            $_FILES["photo"]["error"] !== UPLOAD_ERR_OK
        ) {

            $error =
                "There was a problem uploading the image.";

        } else {


            $allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/gif",
                "image/webp"
            ];


            if (
                !in_array(
                    $_FILES["photo"]["type"],
                    $allowedTypes
                )
            ) {

                $error =
                    "Please upload a valid image.";

            } else {


                $uploadDirectory =
                    "images/posts/";


                if (
                    !is_dir($uploadDirectory)
                ) {

                    mkdir(
                        $uploadDirectory,
                        0775,
                        true
                    );

                }


                $fileExtension =
                    strtolower(
                        pathinfo(
                            $_FILES["photo"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );


                $fileName =
                    uniqid(
                        "post_",
                        true
                    )
                    . "."
                    . $fileExtension;


                $filePath =
                    $uploadDirectory
                    . $fileName;


                if (
                    move_uploaded_file(
                        $_FILES["photo"]["tmp_name"],
                        $filePath
                    )
                ) {

                    $imageURL =
                        $filePath;

                } else {

                    $error =
                        "Could not save the uploaded image.";

                }

            }

        }

    }


    /* =========================
       VALIDATION
    ========================= */

    if ($error === "") {


        if (
            $caption === "" &&
            $movieID === null &&
            $imageURL === null
        ) {

            $error =
                "Please write something or add a movie/photo.";

        }

    }


    /* =========================
       CHECK MOVIE
    ========================= */

    if (
        $error === "" &&
        $movieID !== null
    ) {


        $movieCheck =
            $conn->prepare(
                "SELECT movieID
                 FROM movie
                 WHERE movieID = ?"
            );


        $movieCheck->bind_param(
            "i",
            $movieID
        );


        $movieCheck->execute();


        $movieResult =
            $movieCheck->get_result();


        if (
            $movieResult->num_rows === 0
        ) {

            $error =
                "Selected movie does not exist.";

        }

    }


    /* =========================
       INSERT POST
    ========================= */

    if ($error === "") {


        $postQuery =
            $conn->prepare(
                "INSERT INTO Post
                (
                    userID,
                    movieID,
                    caption,
                    imageURL,
                    watchedDate
                )
                VALUES (?, ?, ?, ?, ?)"
            );


        $postQuery->bind_param(
            "iisss",
            $userID,
            $movieID,
            $caption,
            $imageURL,
            $watchedDate
        );


        if (
            $postQuery->execute()
        ) {

            header(
                "Location: home.php?post=success"
            );

            exit;

        } else {

            $error =
                "Could not create the post.";

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

    <title>
        Create Post - Movie Media
    </title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>

<?php include 'includes/header.php'; ?>


<main class="create-post-container">


    <!-- =========================
         PAGE TITLE
    ========================== -->

    <h1 id="page-title">
        Create Post
    </h1>


    <?php if ($error !== ""): ?>

        <p
            style="
                color: #ff6b6b;
                text-align: center;
                margin-bottom: 20px;
            "
        >
            <?php
            echo htmlspecialchars($error);
            ?>
        </p>

    <?php endif; ?>


    <!-- =========================
         POST FORM
    ========================== -->

    <form
        class="create-post-form"
        id="createPostForm"
        method="POST"
        enctype="multipart/form-data"
    >


        <!-- =========================
             POST CONTENT
        ========================== -->

        <textarea
            id="postContent"
            name="postContent"
            placeholder="What's on your mind?"
        ><?php
            echo htmlspecialchars(
                $_POST["postContent"] ?? ""
            );
        ?></textarea>


        <!-- =========================
             POST OPTIONS
        ========================== -->

        <div class="post-options">


            <!-- ADD MOVIE -->

            <button
                type="button"
                class="post-option"
                id="addMovieBtn"
            >

                <span>
                    Add Movie
                </span>

            </button>


            <!-- ADD PHOTO -->

            <button
                type="button"
                class="post-option"
                id="addPhotoBtn"
            >

                <span>
                    Add Photo
                </span>

            </button>


            <!-- WATCHED DATE -->

            <label
                for="watchedDate"
                class="post-option"
            >

                <span>
                    Watched Date
                </span>

                <input
                    type="date"
                    id="watchedDate"
                    name="watchedDate"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["watchedDate"] ?? ""
                        );
                    ?>"
                >

            </label>


        </div>


        <!-- =========================
             MOVIE SELECTION
        ========================== -->

        <div
            class="movie-selection"
            id="movieSelection"
        >

            <select
                id="movieID"
                name="movieID"
            >

                <option value="">
                    Select a movie
                </option>


                <?php while (
                    $movie = $movieQuery->fetch_assoc()
                ): ?>

                    <option
                        value="<?php
                            echo $movie["movieID"];
                        ?>"
                        <?php

                        if (
                            isset($_POST["movieID"]) &&
                            $_POST["movieID"] ==
                                $movie["movieID"]
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $movie["title"]
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </div>


        <!-- =========================
             PHOTO UPLOAD
        ========================== -->

        <div
            class="photo-selection"
            id="photoSelection"
        >

            <input
                type="file"
                id="photoInput"
                name="photo"
                accept="image/jpeg,image/png,image/gif,image/webp"
            >

        </div>


        <!-- =========================
             POST BUTTON
        ========================== -->

        <div class="post-submit">

            <button type="submit">
                Post
            </button>

        </div>


    </form>


</main>


<script>


/* =========================
   GET ELEMENTS
========================= */

const addMovieBtn =
    document.getElementById(
        "addMovieBtn"
    );


const addPhotoBtn =
    document.getElementById(
        "addPhotoBtn"
    );


const movieSelection =
    document.getElementById(
        "movieSelection"
    );


const photoSelection =
    document.getElementById(
        "photoSelection"
    );


/* =========================
   ADD MOVIE
========================= */

addMovieBtn.addEventListener(
    "click",
    function () {

        movieSelection.classList.toggle(
            "active"
        );

    }
);


/* =========================
   ADD PHOTO
========================= */

addPhotoBtn.addEventListener(
    "click",
    function () {

        photoSelection.classList.toggle(
            "active"
        );

    }
);

</script>


</body>

</html>