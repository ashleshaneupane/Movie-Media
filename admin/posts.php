<?php

include '../includes/adminAuth.php';


/*
|--------------------------------------------------------------------------
| Delete admin post
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    $postID = (int) ($_POST["postID"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($action === "delete") {

        $stmt = $conn->prepare(
            "DELETE FROM Post
             WHERE postID = ?
             AND userID = ?"
        );

        $stmt->bind_param(
            "ii",
            $postID,
            $_SESSION["userID"]
        );

        $stmt->execute();

        $_SESSION["flash"] =
            "Admin post deleted successfully.";
    }

    header("Location: posts.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get admin posts
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        p.postID,
        p.caption,
        p.imageURL,
        p.watchedDate,
        p.postDate
     FROM Post p
     WHERE p.userID = ?
     ORDER BY p.postDate DESC"
);

$stmt->bind_param(
    "i",
    $_SESSION["userID"]
);

$stmt->execute();

$posts = $stmt->get_result();


$pageTitle = "Posts";

include 'adminHeader.php';

?>


<div class="page-heading">

    <div>

        <p class="admin-eyebrow">
            SOCIAL MANAGEMENT
        </p>

        <h1>
            Admin Posts
        </h1>

        <p class="page-subtitle">
            Create and manage posts shown on the Movie Media feed.
        </p>

    </div>

</div>


<div class="movie-actions">

    <a
        href="postForm.php"
        class="btn primary"
    >
        + Create Post
    </a>

</div>


<?php if ($posts->num_rows === 0): ?>

    <div class="movie-empty">

        <h2>
            No admin posts yet
        </h2>

        <p>
            Create your first post to keep the Movie Media feed active.
        </p>

        <a
            href="postForm.php"
            class="btn primary"
        >
            Create First Post
        </a>

    </div>

<?php else: ?>


    <div class="admin-post-list">

        <?php while (
            $post = $posts->fetch_assoc()
        ): ?>


            <?php

            /*
            |--------------------------------------------------------------------------
            | Get movies attached to this post
            |--------------------------------------------------------------------------
            */

            $movieStmt = $conn->prepare(
                "SELECT
                    m.movieID,
                    m.title,
                    m.poster
                 FROM PostMovie pm
                 INNER JOIN movie m
                    ON pm.movieID = m.movieID
                 WHERE pm.postID = ?
                 ORDER BY m.title ASC"
            );

            $movieStmt->bind_param(
                "i",
                $post["postID"]
            );

            $movieStmt->execute();

            $postMovies =
                $movieStmt->get_result();

            ?>


            <article class="admin-post-card">


                <!-- =========================
                     POST HEADER
                ========================== -->

                <div class="admin-post-header">

                    <div>

                        <span class="admin-post-label">
                            ADMIN POST
                        </span>

                        <small>

                            <?php

                            echo e(
                                date(
                                    "M d, Y • h:i A",
                                    strtotime(
                                        $post["postDate"]
                                    )
                                )
                            );

                            ?>

                        </small>

                    </div>

                </div>


                <!-- =========================
                     CAPTION
                ========================== -->

                <?php if (
                    !empty(
                        $post["caption"]
                    )
                ): ?>

                    <p class="admin-post-caption">

                        <?php

                        echo nl2br(
                            e(
                                $post["caption"]
                            )
                        );

                        ?>

                    </p>

                <?php endif; ?>


                <!-- =========================
                     MOVIES
                ========================== -->

                <?php if (
                    $postMovies->num_rows > 0
                ): ?>

                    <div class="admin-post-movies">

                        <?php while (
                            $movie =
                                $postMovies->fetch_assoc()
                        ): ?>

                            <div class="admin-post-movie">

                                <?php if (
                                    !empty(
                                        $movie["poster"]
                                    )
                                ): ?>

                                    <img
                                        src="../<?php
                                            echo e(
                                                $movie["poster"]
                                            );
                                        ?>"
                                        alt="<?php
                                            echo e(
                                                $movie["title"]
                                            );
                                        ?>"
                                    >

                                <?php else: ?>

                                    <div
                                        class="admin-post-movie-placeholder"
                                    >
                                        🎬
                                    </div>

                                <?php endif; ?>


                                <span>

                                    <?php

                                    echo e(
                                        $movie["title"]
                                    );

                                    ?>

                                </span>

                            </div>

                        <?php endwhile; ?>

                    </div>

                <?php endif; ?>


                <!-- =========================
                     POST IMAGE
                ========================== -->

                <?php if (
                    !empty(
                        $post["imageURL"]
                    )
                ): ?>

                    <div class="admin-post-image">

                        <img
                            src="../<?php
                                echo e(
                                    $post["imageURL"]
                                );
                            ?>"
                            alt="Admin post image"
                        >

                    </div>

                <?php endif; ?>


                <!-- =========================
                     WATCHED DATE
                ========================== -->

                <?php if (
                    !empty(
                        $post["watchedDate"]
                    )
                ): ?>

                    <p class="admin-post-date">

                        Watched:

                        <?php

                        echo e(
                            date(
                                "M d, Y",
                                strtotime(
                                    $post["watchedDate"]
                                )
                            )
                        );

                        ?>

                    </p>

                <?php endif; ?>


                <!-- =========================
                     ACTIONS
                ========================== -->

                <div class="movie-card-actions">

                    <a
                        href="postForm.php?id=<?php
                            echo (int)
                                $post["postID"];
                        ?>"
                        class="btn"
                    >
                        Edit
                    </a>


                    <form
                        method="post"
                        class="inline"
                        onsubmit="return confirm('Delete this admin post permanently?');"
                    >

                        <?php echo csrfField(); ?>


                        <input
                            type="hidden"
                            name="action"
                            value="delete"
                        >


                        <input
                            type="hidden"
                            name="postID"
                            value="<?php
                                echo (int)
                                    $post["postID"];
                            ?>"
                        >


                        <button
                            type="submit"
                            class="danger"
                        >
                            Delete
                        </button>

                    </form>

                </div>


            </article>


        <?php endwhile; ?>

    </div>


<?php endif; ?>


<?php include 'adminFooter.php'; ?>