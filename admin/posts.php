
<?php

include '../includes/adminAuth.php';


/*
|--------------------------------------------------------------------------
| Delete post
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    $postID = (int) ($_POST["postID"] ?? 0);
    $action = $_POST["action"] ?? "";


    if ($action === "delete") {

        if ($postID > 0) {

            $stmt = $conn->prepare(
                "DELETE FROM Post
                 WHERE postID = ?"
            );

            $stmt->bind_param(
                "i",
                $postID
            );

            if ($stmt->execute()) {

                $_SESSION["flash"] =
                    "Post deleted successfully.";

            } else {

                $_SESSION["flash"] =
                    "Could not delete post.";
            }

        } else {

            $_SESSION["flash"] =
                "Invalid post.";
        }
    }


    header("Location: posts.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$postsPerPage = 8;

$page = (int) ($_GET["page"] ?? 1);

if ($page < 1) {
    $page = 1;
}


/*
|--------------------------------------------------------------------------
| Count posts
|--------------------------------------------------------------------------
*/

$countStmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM Post"
);

$countStmt->execute();

$countResult =
    $countStmt->get_result();

$totalPosts =
    (int) $countResult
        ->fetch_assoc()["total"];


$totalPages = max(
    1,
    (int) ceil(
        $totalPosts / $postsPerPage
    )
);


if ($page > $totalPages) {
    $page = $totalPages;
}


$offset =
    ($page - 1) * $postsPerPage;


/*
|--------------------------------------------------------------------------
| Get all posts
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        p.postID,
        p.userID,
        p.caption,
        p.imageURL,
        p.watchedDate,
        p.postDate,
        u.name,
        u.username,
        u.profilePicture
     FROM Post p
     INNER JOIN Users u
        ON p.userID = u.userID
     ORDER BY p.postDate DESC
     LIMIT ? OFFSET ?"
);

$stmt->bind_param(
    "ii",
    $postsPerPage,
    $offset
);

$stmt->execute();

$posts =
    $stmt->get_result();


$pageTitle = "Posts";

include 'adminHeader.php';

?>


<div class="page-heading">

    <div>

        <p class="admin-eyebrow">
            SOCIAL MANAGEMENT
        </p>

        <h1>
            User Posts
        </h1>

        <p class="page-subtitle">
            View and manage posts shared by Movie Media users.
        </p>

    </div>

</div>


<?php if ($posts->num_rows === 0): ?>

    <div class="movie-empty">

        <h2>
            No posts yet
        </h2>

        <p>
            User posts will appear here once they are created.
        </p>

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

                    <div class="admin-post-author">


                        <?php if (
                            !empty(
                                $post["profilePicture"]
                            )
                        ): ?>

                            <img
                                src="../<?php
                                    echo e(
                                        $post["profilePicture"]
                                    );
                                ?>"
                                alt="<?php
                                    echo e(
                                        $post["name"]
                                    );
                                ?>"
                                class="admin-post-avatar"
                            >

                        <?php else: ?>

                            <div class="admin-post-avatar-placeholder">
                                <?php
                                echo strtoupper(
                                    substr(
                                        $post["name"],
                                        0,
                                        1
                                    )
                                );
                                ?>
                            </div>

                        <?php endif; ?>


                        <div>

                            <strong>
                                <?php
                                echo e(
                                    $post["name"]
                                );
                                ?>
                            </strong>


                            <small>
                                @<?php
                                echo e(
                                    $post["username"]
                                );
                                ?>
                            </small>


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


                    <span class="admin-post-label">
                        USER POST
                    </span>

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
                            alt="User post image"
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


                    <form
                        method="post"
                        class="inline"
                        onsubmit="return confirm('Delete this post permanently?');"
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


    <!-- =========================
         PAGINATION
    ========================== -->

    <?php if ($totalPages > 1): ?>

        <div class="pagination">


            <?php if ($page > 1): ?>

                <a
                    href="?page=<?php
                        echo $page - 1;
                    ?>"
                >
                    ← Previous
                </a>

            <?php endif; ?>


            <?php for (
                $i = 1;
                $i <= $totalPages;
                $i++
            ): ?>

                <a
                    href="?page=<?php
                        echo $i;
                    ?>"
                    class="<?php echo
                        $i === $page
                        ? "active"
                        : "";
                    ?>"
                >
                    <?php echo $i; ?>
                </a>

            <?php endfor; ?>


            <?php if (
                $page < $totalPages
            ): ?>

                <a
                    href="?page=<?php
                        echo $page + 1;
                    ?>"
                >
                    Next →
                </a>

            <?php endif; ?>


        </div>

    <?php endif; ?>


<?php endif; ?>


<?php include 'adminFooter.php'; ?>
