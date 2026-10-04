
<?php

include '../includes/adminAuth.php';


/*
|--------------------------------------------------------------------------
| Get post ID
|--------------------------------------------------------------------------
*/

$postID = (int) (
    $_GET["id"]
    ?? $_POST["postID"]
    ?? 0
);


/*
|--------------------------------------------------------------------------
| Default post data
|--------------------------------------------------------------------------
*/

$post = [
    "caption" => "",
    "imageURL" => "",
    "watchedDate" => ""
];

$selectedMovies = [];


/*
|--------------------------------------------------------------------------
| Load existing post
|--------------------------------------------------------------------------
*/

if ($postID > 0) {

    $stmt = $conn->prepare(
        "SELECT
            postID,
            caption,
            imageURL,
            watchedDate
         FROM Post
         WHERE postID = ?
         AND userID = ?"
    );

    $stmt->bind_param(
        "ii",
        $postID,
        $_SESSION["userID"]
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $found = $result->fetch_assoc();


    if (!$found) {

        $_SESSION["flash"] =
            "Admin post not found.";

        header("Location: posts.php");

        exit;
    }


    $post = $found;


    /*
    |--------------------------------------------------------------------------
    | Get movies attached to this post
    |--------------------------------------------------------------------------
    */

    $movieStmt = $conn->prepare(
        "SELECT movieID
         FROM PostMovie
         WHERE postID = ?"
    );

    $movieStmt->bind_param(
        "i",
        $postID
    );

    $movieStmt->execute();

    $movieResult =
        $movieStmt->get_result();


    while (
        $movieRow = $movieResult->fetch_assoc()
    ) {

        $selectedMovies[] =
            (int) $movieRow["movieID"];
    }
}


/*
|--------------------------------------------------------------------------
| Get all movies
|--------------------------------------------------------------------------
*/

$movieQuery = $conn->query(
    "SELECT movieID, title
     FROM movie
     ORDER BY title ASC"
);


/*
|--------------------------------------------------------------------------
| Error
|--------------------------------------------------------------------------
*/

$error = "";


/*
|--------------------------------------------------------------------------
| Handle form submission
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();


    /*
    |--------------------------------------------------------------------------
    | Get caption
    |--------------------------------------------------------------------------
    */

    $caption = trim(
        $_POST["postContent"] ?? ""
    );


    /*
    |--------------------------------------------------------------------------
    | Get selected movies
    |--------------------------------------------------------------------------
    */

    $selectedMovies = [];


    if (
        isset($_POST["movieIDs"]) &&
        is_array($_POST["movieIDs"])
    ) {

        foreach (
            $_POST["movieIDs"]
            as $movieID
        ) {

            $movieID = (int) $movieID;

            if ($movieID > 0) {

                $selectedMovies[] =
                    $movieID;
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Remove duplicate movie IDs
    |--------------------------------------------------------------------------
    */

    $selectedMovies =
        array_values(
            array_unique(
                $selectedMovies
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Watched date
    |--------------------------------------------------------------------------
    */

    $watchedDate = !empty(
        $_POST["watchedDate"]
    )
        ? $_POST["watchedDate"]
        : null;


    /*
    |--------------------------------------------------------------------------
    | Existing image
    |--------------------------------------------------------------------------
    */

    $imageURL =
        $post["imageURL"] ?? null;


    /*
    |--------------------------------------------------------------------------
    | Upload new photo
    |--------------------------------------------------------------------------
    */

    if (
        isset($_FILES["photo"]) &&
        $_FILES["photo"]["error"]
            !== UPLOAD_ERR_NO_FILE
    ) {

        if (
            $_FILES["photo"]["error"]
            !== UPLOAD_ERR_OK
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
                    "../images/posts/";


                if (
                    !is_dir(
                        $uploadDirectory
                    )
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
                        "admin_post_",
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
                        "images/posts/"
                        . $fileName;

                } else {

                    $error =
                        "Could not save the uploaded image.";

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        if (
            $caption === "" &&
            empty($selectedMovies) &&
            empty($imageURL)
        ) {

            $error =
                "Please write something, add a movie, or add a photo.";

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Validate watched date
    |--------------------------------------------------------------------------
    */

    if (
        $error === "" &&
        $watchedDate !== null
    ) {

        $dateCheck =
            DateTime::createFromFormat(
                "Y-m-d",
                $watchedDate
            );


        if (
            !$dateCheck ||
            $dateCheck->format("Y-m-d")
                !== $watchedDate
        ) {

            $error =
                "Please enter a valid watched date.";

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Check selected movies
    |--------------------------------------------------------------------------
    */

    if (
        $error === "" &&
        !empty($selectedMovies)
    ) {

        $placeholders =
            implode(
                ",",
                array_fill(
                    0,
                    count($selectedMovies),
                    "?"
                )
            );


        $types =
            str_repeat(
                "i",
                count($selectedMovies)
            );


        $movieCheck = $conn->prepare(
            "SELECT movieID
             FROM movie
             WHERE movieID IN ($placeholders)"
        );


        $movieCheck->bind_param(
            $types,
            ...$selectedMovies
        );


        $movieCheck->execute();


        $movieResult =
            $movieCheck->get_result();


        $validMovieIDs = [];


        while (
            $movieRow =
                $movieResult->fetch_assoc()
        ) {

            $validMovieIDs[] =
                (int) $movieRow["movieID"];
        }


        sort($validMovieIDs);

        $checkMovies =
            $selectedMovies;

        sort($checkMovies);


        if (
            $validMovieIDs !==
            $checkMovies
        ) {

            $error =
                "One or more selected movies do not exist.";

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Save post
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        $conn->begin_transaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | CREATE
            |--------------------------------------------------------------------------
            */

            if ($postID === 0) {

                /*
                | Keep movieID NULL in Post.
                | Admin movies are stored in PostMovie.
                */

                $adminUserID =
                    (int) $_SESSION["userID"];


                $stmt = $conn->prepare(
                    "INSERT INTO Post
                    (
                        userID,
                        movieID,
                        caption,
                        imageURL,
                        watchedDate
                    )
                    VALUES (?, NULL, ?, ?, ?)"
                );


                $stmt->bind_param(
                    "isss",
                    $adminUserID,
                    $caption,
                    $imageURL,
                    $watchedDate
                );


                $stmt->execute();


                $postID =
                    $stmt->insert_id;


                /*
                |--------------------------------------------------------------------------
                | Attach selected movies
                |--------------------------------------------------------------------------
                */

                if (
                    !empty($selectedMovies)
                ) {

                    $movieInsert =
                        $conn->prepare(
                            "INSERT INTO PostMovie
                            (
                                postID,
                                movieID
                            )
                            VALUES (?, ?)"
                        );


                    foreach (
                        $selectedMovies
                        as $movieID
                    ) {

                        $movieInsert->bind_param(
                            "ii",
                            $postID,
                            $movieID
                        );

                        $movieInsert->execute();
                    }
                }


                $conn->commit();


                $_SESSION["flash"] =
                    "Admin post created successfully.";


                header(
                    "Location: posts.php"
                );

                exit;

            }


            /*
            |--------------------------------------------------------------------------
            | EDIT
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "UPDATE Post
                 SET
                    caption = ?,
                    imageURL = ?,
                    watchedDate = ?
                 WHERE postID = ?
                 AND userID = ?"
            );


            $adminUserID =
                (int) $_SESSION["userID"];


            $stmt->bind_param(
                "sssii",
                $caption,
                $imageURL,
                $watchedDate,
                $postID,
                $adminUserID
            );


            $stmt->execute();


            /*
            |--------------------------------------------------------------------------
            | Remove old movie relationships
            |--------------------------------------------------------------------------
            */

            $deleteMovies =
                $conn->prepare(
                    "DELETE FROM PostMovie
                     WHERE postID = ?"
                );


            $deleteMovies->bind_param(
                "i",
                $postID
            );


            $deleteMovies->execute();


            /*
            |--------------------------------------------------------------------------
            | Add new movie relationships
            |--------------------------------------------------------------------------
            */

            if (
                !empty($selectedMovies)
            ) {

                $movieInsert =
                    $conn->prepare(
                        "INSERT INTO PostMovie
                        (
                            postID,
                            movieID
                        )
                        VALUES (?, ?)"
                    );


                foreach (
                    $selectedMovies
                    as $movieID
                ) {

                    $movieInsert->bind_param(
                        "ii",
                        $postID,
                        $movieID
                    );

                    $movieInsert->execute();
                }
            }


            $conn->commit();


            $_SESSION["flash"] =
                "Admin post updated successfully.";


            header(
                "Location: posts.php"
            );

            exit;


        } catch (
            Throwable $exception
        ) {

            $conn->rollback();

            $error =
                "Could not save the post.";
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Keep entered values if validation fails
    |--------------------------------------------------------------------------
    */

    $post["caption"] =
        $caption;

    $post["watchedDate"] =
        $watchedDate ?? "";

    $post["imageURL"] =
        $imageURL ?? "";

}


/*
|--------------------------------------------------------------------------
| Page title
|--------------------------------------------------------------------------
*/

$pageTitle =
    $postID > 0
        ? "Edit Admin Post"
        : "Create Admin Post";


include 'adminHeader.php';

?>


<div class="page-heading">

    <div>

        <p class="admin-eyebrow">
            SOCIAL MANAGEMENT
        </p>

        <h1>
            <?php echo e($pageTitle); ?>
        </h1>

        <p class="page-subtitle">
            Create movie announcements, recommendations and featured content.
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
    enctype="multipart/form-data"
>


    <?php echo csrfField(); ?>


    <input
        type="hidden"
        name="postID"
        value="<?php echo (int) $postID; ?>"
    >


    <!-- =========================
         POST CONTENT
    ========================== -->

    <section class="form-section">

        <h2>
            Post Content
        </h2>


        <label>

            Caption

            <textarea
                name="postContent"
                rows="6"
                placeholder="Write something for the Movie Media community..."
            ><?php
                echo e(
                    $post["caption"]
                );
            ?></textarea>

        </label>

    </section>


    <!-- =========================
         MOVIES
    ========================== -->

    <section class="form-section">

        <h2>
            Movies
        </h2>


        <p class="form-help">
            Select one or multiple movies to attach to this post.
        </p>


        <div class="admin-movie-select">

            <?php while (
                $movie =
                    $movieQuery->fetch_assoc()
            ): ?>

                <label class="movie-checkbox">

                    <input
                        type="checkbox"
                        name="movieIDs[]"
                        value="<?php
                            echo (int)
                                $movie["movieID"];
                        ?>"
                        <?php

                        echo in_array(
                            (int)
                                $movie["movieID"],
                            $selectedMovies,
                            true
                        )
                            ? "checked"
                            : "";

                        ?>
                    >

                    <span>
                        <?php
                        echo e(
                            $movie["title"]
                        );
                        ?>
                    </span>

                </label>

            <?php endwhile; ?>

        </div>

    </section>


    <!-- =========================
         PHOTO
    ========================== -->

    <section class="form-section">

        <h2>
            Photo
        </h2>


        <?php if (
            !empty(
                $post["imageURL"]
            )
        ): ?>

            <div class="existing-post-image">

                <p>
                    Current image
                </p>

                <img
                    src="../<?php
                        echo e(
                            $post["imageURL"]
                        );
                    ?>"
                    alt="Current post image"
                >

            </div>

        <?php endif; ?>


        <label>

            Upload image

            <input
                type="file"
                name="photo"
                accept="image/jpeg,image/png,image/gif,image/webp"
            >

        </label>


        <small class="form-help">

            Leave empty to keep the current image.

        </small>

    </section>


    <!-- =========================
         WATCHED DATE
    ========================== -->

    <section class="form-section">

        <h2>
            Date
        </h2>


        <label>

            Watched Date

            <input
                type="date"
                name="watchedDate"
                value="<?php
                    echo e(
                        $post["watchedDate"]
                    );
                ?>"
            >

        </label>

    </section>


    <!-- =========================
         ACTIONS
    ========================== -->

    <div class="form-actions">

        <button
            type="submit"
            class="primary"
        >

            <?php

            echo $postID > 0
                ? "Save Changes"
                : "Create Post";

            ?>

        </button>


        <a
            href="posts.php"
            class="btn secondary-btn"
        >
            Cancel
        </a>

    </div>


</form>


<?php include 'adminFooter.php'; ?>
