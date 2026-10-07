
<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];


/* =========================
   GET CURRENT USER
========================= */

$userQuery = $conn->prepare(
    "SELECT
        userID,
        username,
        name,
        profilePicture,
        bio,
        favoriteGenre,
        role
     FROM Users
     WHERE userID = ?"
);

$userQuery->bind_param(
    "i",
    $userID
);

$userQuery->execute();

$user = $userQuery
    ->get_result()
    ->fetch_assoc();


/* =========================
   WATCHED COUNT
========================= */

$watchedQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM Watched
     WHERE userID = ?"
);

$watchedQuery->bind_param(
    "i",
    $userID
);

$watchedQuery->execute();

$watchedCount =
    $watchedQuery
    ->get_result()
    ->fetch_assoc()["total"];


/* =========================
   WATCHLIST COUNT
========================= */

$watchlistQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM Watchlist
     WHERE userID = ?"
);

$watchlistQuery->bind_param(
    "i",
    $userID
);

$watchlistQuery->execute();

$watchlistCount =
    $watchlistQuery
    ->get_result()
    ->fetch_assoc()["total"];


/* =========================
   REVIEW COUNT + AVERAGE
========================= */

$reviewQuery = $conn->prepare(
    "SELECT
        COUNT(*) AS total,
        AVG(rating) AS averageRating
     FROM Review
     WHERE userID = ?"
);

$reviewQuery->bind_param(
    "i",
    $userID
);

$reviewQuery->execute();

$reviewStats =
    $reviewQuery
    ->get_result()
    ->fetch_assoc();

$reviewCount =
    $reviewStats["total"];

$averageRating =
    $reviewStats["averageRating"];


/* =========================
   POST COUNT
========================= */

$postCountQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM Post
     WHERE userID = ?"
);

$postCountQuery->bind_param(
    "i",
    $userID
);

$postCountQuery->execute();

$postCount =
    $postCountQuery
    ->get_result()
    ->fetch_assoc()["total"];


/* =========================
   FRIEND COUNT
========================= */

$friendCountQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM FriendRequest
     WHERE status = 'accepted'
     AND (
         senderID = ?
         OR receiverID = ?
     )"
);

$friendCountQuery->bind_param(
    "ii",
    $userID,
    $userID
);

$friendCountQuery->execute();

$friendCount =
    $friendCountQuery
    ->get_result()
    ->fetch_assoc()["total"];


/* =========================
   TOP 5 MOVIES
========================= */

$topMovieQuery = $conn->prepare(
    "SELECT
        movie.title
     FROM UserTopMovies
     INNER JOIN movie
        ON UserTopMovies.movieID = movie.movieID
     WHERE UserTopMovies.userID = ?
     ORDER BY UserTopMovies.position ASC
     LIMIT 5"
);

$topMovieQuery->bind_param(
    "i",
    $userID
);

$topMovieQuery->execute();

$topMovieResult =
    $topMovieQuery->get_result();


/* =========================
   FAVORITE GENRES
========================= */

$favoriteGenres = [];

if (!empty($user["favoriteGenre"])) {

    $favoriteGenres =
        array_filter(
            array_map(
                "trim",
                explode(
                    ",",
                    $user["favoriteGenre"]
                )
            )
        );

}


/* =========================
   GET POSTS
========================= */

/*
 * ADMIN:
 * See every post on Movie Media.
 *
 * NORMAL USER:
 * See:
 * - their own posts
 * - accepted friends' posts
 * - admin posts
 */

if ($user["role"] === "admin") {

    $postQuery = $conn->prepare(
        "SELECT
            Post.postID,
            Post.userID,
            Post.movieID,
            Post.caption,
            Post.imageURL,
            Post.watchedDate,
            Post.postDate,

            Users.username,
            Users.name,
            Users.profilePicture,
            Users.role,

            movie.title AS movieTitle,
            movie.poster AS moviePoster,

            (
                SELECT COUNT(*)
                FROM PostLike
                WHERE PostLike.postID = Post.postID
            ) AS likeCount,

            (
                SELECT COUNT(*)
                FROM PostLike
                WHERE PostLike.postID = Post.postID
                AND PostLike.userID = ?
            ) AS userLiked,

            (
                SELECT COUNT(*)
                FROM PostComment
                WHERE PostComment.postID = Post.postID
            ) AS commentCount

         FROM Post

         INNER JOIN Users
            ON Post.userID = Users.userID

         LEFT JOIN movie
            ON Post.movieID = movie.movieID

         ORDER BY Post.postDate DESC"
    );

    $postQuery->bind_param(
        "i",
        $userID
    );

} else {

    $postQuery = $conn->prepare(
        "SELECT
            Post.postID,
            Post.userID,
            Post.movieID,
            Post.caption,
            Post.imageURL,
            Post.watchedDate,
            Post.postDate,

            Users.username,
            Users.name,
            Users.profilePicture,
            Users.role,

            movie.title AS movieTitle,
            movie.poster AS moviePoster,

            (
                SELECT COUNT(*)
                FROM PostLike
                WHERE PostLike.postID = Post.postID
            ) AS likeCount,

            (
                SELECT COUNT(*)
                FROM PostLike
                WHERE PostLike.postID = Post.postID
                AND PostLike.userID = ?
            ) AS userLiked,

            (
                SELECT COUNT(*)
                FROM PostComment
                WHERE PostComment.postID = Post.postID
            ) AS commentCount

         FROM Post

         INNER JOIN Users
            ON Post.userID = Users.userID

         LEFT JOIN movie
            ON Post.movieID = movie.movieID

         WHERE

            Post.userID = ?

            OR

            Post.userID IN (
                SELECT
                    CASE
                        WHEN senderID = ?
                        THEN receiverID
                        ELSE senderID
                    END

                FROM FriendRequest

                WHERE
                    status = 'accepted'

                    AND (
                        senderID = ?
                        OR receiverID = ?
                    )
            )

            OR

            Post.userID IN (
                SELECT userID
                FROM Users
                WHERE role = 'admin'
            )

         ORDER BY Post.postDate DESC"
    );

    $postQuery->bind_param(
        "iiiii",
        $userID,
        $userID,
        $userID,
        $userID,
        $userID
    );
}


$postQuery->execute();

$postQuery =
    $postQuery->get_result();


/* =========================
   GET MULTIPLE MOVIES
   FOR POSTS
========================= */

$postMovies = [];

$movieLinksQuery = $conn->prepare(
    "SELECT
        pm.postID,
        m.movieID,
        m.title,
        m.poster

     FROM PostMovie pm

     INNER JOIN movie m
        ON pm.movieID = m.movieID

     ORDER BY m.title ASC"
);

$movieLinksQuery->execute();

$movieLinksResult =
    $movieLinksQuery->get_result();

while (
    $movieLink =
    $movieLinksResult->fetch_assoc()
) {

    $postMovies[
        $movieLink["postID"]
    ][] = $movieLink;
}


/* =========================
   PROFILE IMAGE
========================= */

$profilePicture =
    !empty($user["profilePicture"])
    ? $user["profilePicture"]
    : "";

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
        Home - Movie Media
    </title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body class="home-page">

<?php include 'includes/header.php'; ?>


<main class="home-layout">


    <!-- =========================
         LEFT SIDEBAR
    ========================== -->

    <aside class="left-sidebar">

        <div class="profile-card">


            <!-- PROFILE IMAGE -->

            <div class="profile-image">

                <?php if ($profilePicture !== ""): ?>

                    <img
                        src="<?php
                            echo htmlspecialchars(
                                $profilePicture
                            );
                        ?>"
                        alt="Profile Picture"
                    >

                <?php else: ?>

                    <span>

                        <?php

                        echo strtoupper(
                            substr(
                                $user["name"]
                                ?: $user["username"],
                                0,
                                1
                            )
                        );

                        ?>

                    </span>

                <?php endif; ?>

            </div>


            <!-- NAME -->

            <h2>

                <?php

                echo htmlspecialchars(
                    $user["name"]
                    ?: $user["username"]
                );

                ?>

            </h2>


            <!-- USERNAME -->

            <p class="username">

                @<?php
                echo htmlspecialchars(
                    $user["username"]
                );
                ?>

            </p>


            <!-- BIO -->

            <p class="profile-bio">

                <?php

                if (!empty($user["bio"])) {

                    echo htmlspecialchars(
                        $user["bio"]
                    );

                } else {

                    echo "Movie lover, escaping reality.";

                }

                ?>

            </p>


            <!-- PROFILE STATS -->

            <div class="profile-stats">


                <div class="profile-stat">

                    <strong>
                        <?php echo $postCount; ?>
                    </strong>

                    <span>
                        Posts
                    </span>

                </div>


                <div class="profile-stat">

                    <strong>
                        <?php echo $watchedCount; ?>
                    </strong>

                    <span>
                        Watched
                    </span>

                </div>


                <div class="profile-stat">

                    <strong>
                        <?php echo $friendCount; ?>
                    </strong>

                    <span>
                        Friends
                    </span>

                </div>


            </div>


            <!-- MOVIE STATS -->

            <div class="movie-stats">


                <div class="movie-stat-row">

                    <span>
                        Watchlist
                    </span>

                    <strong>
                        <?php echo $watchlistCount; ?>
                    </strong>

                </div>


                <div class="movie-stat-row">

                    <span>
                        Reviews
                    </span>

                    <strong>
                        <?php echo $reviewCount; ?>
                    </strong>

                </div>


                <div class="movie-stat-row">

                    <span>
                        Avg. Rating
                    </span>

                    <strong>

                        <?php

                        if ($averageRating === null) {

                            echo "0.0";

                        } else {

                            echo number_format(
                                (float) $averageRating,
                                1
                            );

                        }

                        ?>

                    </strong>

                </div>


            </div>


            <!-- FAVORITE GENRES -->

            <div class="profile-section">

                <h3>
                    Favorite Genres
                </h3>


                <div class="genre-list">

                    <?php if (!empty($favoriteGenres)): ?>

                        <?php foreach (
                            $favoriteGenres as $genre
                        ): ?>

                            <span>

                                <?php
                                echo htmlspecialchars(
                                    $genre
                                );
                                ?>

                            </span>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <span>
                            No genres selected
                        </span>

                    <?php endif; ?>

                </div>

            </div>


            <!-- TOP 5 -->

            <div class="profile-section">

                <h3>
                    My Top 5
                </h3>


                <?php if (
                    $topMovieResult->num_rows > 0
                ): ?>

                    <ol class="top-movies">

                        <?php while (
                            $topMovie =
                            $topMovieResult->fetch_assoc()
                        ): ?>

                            <li>

                                <?php
                                echo htmlspecialchars(
                                    $topMovie["title"]
                                );
                                ?>

                            </li>

                        <?php endwhile; ?>

                    </ol>

                <?php else: ?>

                    <p>
                        No top movies added yet.
                    </p>

                <?php endif; ?>


            </div>


            <!-- VIEW PROFILE -->

            <a
                href="account.php"
                class="view-profile-btn"
            >
                View Profile
            </a>


        </div>

    </aside>



    <!-- =========================
         CENTER FEED
    ========================== -->

    <section class="home-feed">


        <!-- CREATE POST -->

        <div class="create-post-link">

            <a href="createPost.php">

                <span class="plus-icon">
                    +
                </span>

                Create Post

            </a>

        </div>


        <!-- =========================
             POSTS
        ========================= -->

        <?php if (
            $postQuery->num_rows === 0
        ): ?>

            <article class="post-card">

                <p
                    style="
                        color: white;
                        text-align: center;
                    "
                >
                    No posts yet. Be the first to post!
                </p>

            </article>


        <?php else: ?>


            <?php while (
                $post =
                $postQuery->fetch_assoc()
            ): ?>


                <article
                    class="post-card"
                    data-post-id="<?php
                        echo $post["postID"];
                    ?>"
                >


                    <!-- =========================
                         POST HEADER
                    ========================== -->

                    <div class="post-header">

                        <?php

                        $isAdminPost =
                            isset($post["role"]) &&
                            $post["role"] === "admin";

                        ?>


                        <?php if ($isAdminPost): ?>


                            <!-- ADMIN POST -->

                            <div class="post-user-link admin-post-user">

                                <div class="post-profile-image admin-profile-icon">

                                    🎬

                                </div>

                                <div class="post-user-info">

                                    <h3>
                                        Movie Media
                                    </h3>

                                    <p class="admin-badge">
                                        ADMIN
                                    </p>

                                </div>

                            </div>


                        <?php else: ?>


                            <!-- NORMAL USER POST -->

                            <a
                                href="profile.php?user=<?php
                                    echo $post["userID"];
                                ?>"
                                class="post-user-link"
                            >

                                <div class="post-profile-image">

                                    <?php if (
                                        !empty(
                                            $post["profilePicture"]
                                        )
                                    ): ?>

                                        <img
                                            src="<?php
                                                echo htmlspecialchars(
                                                    $post["profilePicture"]
                                                );
                                            ?>"
                                            alt="Profile Picture"
                                        >

                                    <?php else: ?>

                                        <?php

                                        echo strtoupper(
                                            substr(
                                                $post["name"]
                                                ?: $post["username"],
                                                0,
                                                1
                                            )
                                        );

                                        ?>

                                    <?php endif; ?>

                                </div>


                                <div class="post-user-info">

                                    <h3>

                                        <?php

                                        echo htmlspecialchars(
                                            $post["name"]
                                            ?: $post["username"]
                                        );

                                        ?>

                                    </h3>


                                    <p>

                                        @<?php

                                        echo htmlspecialchars(
                                            $post["username"]
                                        );

                                        ?>

                                    </p>

                                </div>

                            </a>


                        <?php endif; ?>


                        <!-- DELETE OWN POST -->

                        <?php if (
                            !$isAdminPost &&
                            (int) $post["userID"] ===
                            (int) $userID
                        ): ?>

                            <button
                                type="button"
                                class="delete-post"
                                data-post-id="<?php
                                    echo $post["postID"];
                                ?>"
                            >
                                Delete
                            </button>

                        <?php endif; ?>


                    </div>


                    <!-- =========================
                         POST CONTENT
                    ========================== -->

                    <div class="post-content">


                        <!-- CAPTION -->

                        <?php if (
                            !empty(
                                $post["caption"]
                            )
                        ): ?>

                            <p>

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $post["caption"]
                                    )
                                );

                                ?>

                            </p>

                        <?php endif; ?>


                        <!-- =========================
                             MOVIES
                        ========================== -->

                        <?php

                        $linkedMovies =
                            $postMovies[$post["postID"]]
                            ?? [];

                        ?>


                        <!-- MULTIPLE MOVIES -->

                        <?php if (!empty($linkedMovies)): ?>

                            <div class="movie-attachments">

                                <?php foreach (
                                    $linkedMovies as $linkedMovie
                                ): ?>

                                    <div class="movie-attachment">

                                        <?php if (
                                            !empty(
                                                $linkedMovie["poster"]
                                            )
                                        ): ?>

                                            <img
                                                src="<?php
                                                    echo htmlspecialchars(
                                                        $linkedMovie["poster"]
                                                    );
                                                ?>"
                                                alt="<?php
                                                    echo htmlspecialchars(
                                                        $linkedMovie["title"]
                                                    );
                                                ?>"
                                                class="movie-poster"
                                            >

                                        <?php endif; ?>


                                        <div class="movie-attachment-info">

                                            <h3>

                                                <?php
                                                echo htmlspecialchars(
                                                    $linkedMovie["title"]
                                                );
                                                ?>

                                            </h3>


                                            <?php if (
                                                !empty(
                                                    $post["watchedDate"]
                                                )
                                            ): ?>

                                                <p>

                                                    Watched:

                                                    <?php

                                                    echo date(
                                                        "F d, Y",
                                                        strtotime(
                                                            $post["watchedDate"]
                                                        )
                                                    );

                                                    ?>

                                                </p>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>


                        <!-- OLD SINGLE-MOVIE POSTS -->

                        <?php if (
                            empty($linkedMovies)
                            &&
                            !empty($post["movieID"])
                            &&
                            !empty($post["movieTitle"])
                        ): ?>

                            <div class="movie-attachment">

                                <?php if (
                                    !empty(
                                        $post["moviePoster"]
                                    )
                                ): ?>

                                    <img
                                        src="<?php
                                            echo htmlspecialchars(
                                                $post["moviePoster"]
                                            );
                                        ?>"
                                        alt="<?php
                                            echo htmlspecialchars(
                                                $post["movieTitle"]
                                            );
                                        ?>"
                                        class="movie-poster"
                                    >

                                <?php endif; ?>


                                <div class="movie-attachment-info">

                                    <h3>

                                        <?php
                                        echo htmlspecialchars(
                                            $post["movieTitle"]
                                        );
                                        ?>

                                    </h3>


                                    <?php if (
                                        !empty(
                                            $post["watchedDate"]
                                        )
                                    ): ?>

                                        <p>

                                            Watched:

                                            <?php

                                            echo date(
                                                "F d, Y",
                                                strtotime(
                                                    $post["watchedDate"]
                                                )
                                            );

                                            ?>

                                        </p>

                                    <?php endif; ?>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- PHOTO -->

                        <?php if (
                            !empty(
                                $post["imageURL"]
                            )
                        ): ?>

                            <div class="photo-attachment">

                                <img
                                    src="<?php
                                        echo htmlspecialchars(
                                            $post["imageURL"]
                                        );
                                    ?>"
                                    alt="Post Image"
                                    class="post-image"
                                >

                            </div>

                        <?php endif; ?>


                    </div>


                    <!-- =========================
                         POST ACTIONS
                    ========================== -->

                    <div class="post-actions">


                        <!-- LIKE -->

                        <button
                            type="button"
                            class="like-button <?php
                                echo $post["userLiked"] > 0
                                    ? "liked"
                                    : "";
                            ?>"
                            data-post-id="<?php
                                echo $post["postID"];
                            ?>"
                        >

                            <?php

                            echo $post["userLiked"] > 0
                                ? "★"
                                : "☆";

                            ?>

                            <span>

                                <?php
                                echo $post["likeCount"];
                                ?>

                            </span>

                        </button>


                        <!-- COMMENT -->

                        <button
                            type="button"
                            class="comment-toggle"
                            data-post-id="<?php
                                echo $post["postID"];
                            ?>"
                        >

                            ▤

                            <span
                                class="comment-count"
                            >

                                <?php
                                echo $post["commentCount"];
                                ?>

                            </span>

                        </button>


                    </div>


                </article>


            <?php endwhile; ?>


        <?php endif; ?>


    </section>



    <!-- =========================
         RIGHT SIDEBAR
    ========================== -->

    <aside
        class="right-sidebar"
        id="rightSidebar"
    >

        <div
            class="comments-card"
            id="commentsCard"
        >

            <div class="comments-header">

                <h2>
                    Comments
                </h2>

                <button
                    type="button"
                    id="closeComments"
                >
                    ×
                </button>

            </div>


            <div
                class="sidebar-comment-list"
                id="sidebarCommentList"
            >

                <p class="no-comments">
                    Click the comment icon on a post.
                </p>

            </div>


            <div class="sidebar-comment-form">

                <input
                    type="text"
                    id="sidebarCommentInput"
                    placeholder="Write a comment..."
                    disabled
                >

                <button
                    type="button"
                    id="sidebarCommentButton"
                    disabled
                >
                    Post
                </button>

            </div>

        </div>

    </aside>


</main>



<script>

const currentUserID =
    <?php echo (int) $userID; ?>;


/* =========================
   LIKE SYSTEM
========================= */

const likeButtons =
    document.querySelectorAll(
        ".like-button"
    );


likeButtons.forEach(function(button) {

    button.addEventListener(
        "click",
        function() {

            const postID =
                button.dataset.postId;


            const count =
                button.querySelector(
                    "span"
                );


            const formData =
                new FormData();


            formData.append(
                "postID",
                postID
            );


            fetch(
                "likePost.php",
                {
                    method: "POST",
                    body: formData
                }
            )

            .then(function(response) {

                return response.text();

            })

            .then(function(result) {

                if (
                    result === "liked"
                ) {

                    button.classList.add(
                        "liked"
                    );


                    button.firstChild.textContent =
                        "★ ";


                    count.textContent =
                        parseInt(
                            count.textContent
                        ) + 1;

                }


                else if (
                    result === "unliked"
                ) {

                    button.classList.remove(
                        "liked"
                    );


                    button.firstChild.textContent =
                        "☆ ";


                    count.textContent =
                        Math.max(
                            0,
                            parseInt(
                                count.textContent
                            ) - 1
                        );

                }

            });

        }
    );

});


/* =========================
   COMMENTS
========================= */

let selectedPostID = null;


/* =========================
   COMMENT BUTTONS
========================= */

const commentToggles =
    document.querySelectorAll(
        ".comment-toggle"
    );


commentToggles.forEach(function(button) {

    button.addEventListener(
        "click",
        function() {

            selectedPostID =
                button.dataset.postId;


            const sidebar =
                document.getElementById(
                    "rightSidebar"
                );


            sidebar.classList.add(
                "active"
            );


            loadComments(
                selectedPostID
            );

        }
    );

});


/* =========================
   LOAD COMMENTS
========================= */

function loadComments(postID) {

    const commentList =
        document.getElementById(
            "sidebarCommentList"
        );


    const input =
        document.getElementById(
            "sidebarCommentInput"
        );


    const submitButton =
        document.getElementById(
            "sidebarCommentButton"
        );


    commentList.innerHTML =
        "<p>Loading comments...</p>";


    input.disabled = false;

    submitButton.disabled = false;


    fetch(
        "getComments.php?postID=" +
        encodeURIComponent(postID)
    )

    .then(function(response) {

        return response.json();

    })

    .then(function(comments) {

        commentList.innerHTML = "";


        if (
            comments.length === 0
        ) {

            commentList.innerHTML =
                '<p class="no-comments">No comments yet.</p>';

            return;

        }


        comments.forEach(function(comment) {

            const commentElement =
                document.createElement(
                    "div"
                );


            commentElement.className =
                "sidebar-comment";


            const commentTop =
                document.createElement(
                    "div"
                );


            commentTop.className =
                "sidebar-comment-top";


            const username =
                document.createElement(
                    "strong"
                );


            if (
                comment.role === "admin"
            ) {

                username.textContent =
                    "🎬 Movie Media • ADMIN";

            } else {

                username.textContent =
                    "@" + comment.username;

            }


            commentTop.appendChild(
                username
            );


            /* =========================
               DELETE BUTTON
            ========================= */

            if (
                Number(comment.userID) ===
                Number(currentUserID)
            ) {

                const deleteButton =
                    document.createElement(
                        "button"
                    );


                deleteButton.className =
                    "delete-comment";


                deleteButton.textContent =
                    "Delete";


                deleteButton.addEventListener(
                    "click",
                    function() {

                        deleteComment(
                            comment.commentID
                        );

                    }
                );


                commentTop.appendChild(
                    deleteButton
                );

            }


            const text =
                document.createElement(
                    "p"
                );


            text.textContent =
                comment.commentText;


            commentElement.appendChild(
                commentTop
            );


            commentElement.appendChild(
                text
            );


            commentList.appendChild(
                commentElement
            );

        });

    })

    .catch(function() {

        commentList.innerHTML =
            '<p class="no-comments">Could not load comments.</p>';

    });

}


/* =========================
   DELETE COMMENT
========================= */

function deleteComment(commentID) {

    const formData =
        new FormData();


    formData.append(
        "commentID",
        commentID
    );


    fetch(
        "deleteComment.php",
        {
            method: "POST",
            body: formData
        }
    )

    .then(function(response) {

        return response.text();

    })

    .then(function(result) {

        if (
            result === "success"
        ) {

            loadComments(
                selectedPostID
            );


            const selectedButton =
                document.querySelector(
                    '.comment-toggle[data-post-id="' +
                    selectedPostID +
                    '"]'
                );


            if (
                selectedButton
            ) {

                const count =
                    selectedButton.querySelector(
                        ".comment-count"
                    );


                count.textContent =
                    Math.max(
                        0,
                        parseInt(
                            count.textContent
                        ) - 1
                    );

            }

        }

        else {

            alert(
                "Could not delete comment."
            );

        }

    });

}


/* =========================
   ADD COMMENT
========================= */

document
    .getElementById(
        "sidebarCommentButton"
    )
    .addEventListener(
        "click",
        function() {

            const input =
                document.getElementById(
                    "sidebarCommentInput"
                );


            const commentText =
                input.value.trim();


            if (
                selectedPostID === null
            ) {

                alert(
                    "Please select a post first."
                );

                return;

            }


            if (
                commentText === ""
            ) {

                alert(
                    "Please write a comment."
                );

                return;

            }


            const formData =
                new FormData();


            formData.append(
                "postID",
                selectedPostID
            );


            formData.append(
                "commentText",
                commentText
            );


            fetch(
                "addComment.php",
                {
                    method: "POST",
                    body: formData
                }
            )

            .then(function(response) {

                return response.text();

            })

            .then(function(result) {

                if (
                    result === "success"
                ) {

                    input.value = "";


                    loadComments(
                        selectedPostID
                    );


                    const selectedButton =
                        document.querySelector(
                            '.comment-toggle[data-post-id="' +
                            selectedPostID +
                            '"]'
                        );


                    if (
                        selectedButton
                    ) {

                        const count =
                            selectedButton.querySelector(
                                ".comment-count"
                            );


                        count.textContent =
                            parseInt(
                                count.textContent
                            ) + 1;

                    }

                }

                else {

                    alert(
                        "Could not add comment."
                    );

                }

            });

        }
    );


/* =========================
   CLOSE COMMENTS
========================= */

document
    .getElementById(
        "closeComments"
    )
    .addEventListener(
        "click",
        function() {

            document
                .getElementById(
                    "rightSidebar"
                )
                .classList.remove(
                    "active"
                );


            selectedPostID = null;

        }
    );


/* =========================
   DELETE POST
========================= */

document
    .querySelectorAll(".delete-post")
    .forEach(function(button) {

        button.addEventListener(
            "click",
            function() {

                const postID =
                    button.dataset.postId;


                const formData =
                    new FormData();


                formData.append(
                    "postID",
                    postID
                );


                fetch(
                    "deletePost.php",
                    {
                        method: "POST",
                        body: formData
                    }
                )

                .then(function(response) {

                    return response.text();

                })

                .then(function(result) {

                    if (
                        result === "success"
                    ) {

                        const postCard =
                            button.closest(
                                ".post-card"
                            );


                        if (postCard) {

                            postCard.remove();

                        }

                    }

                    else {

                        alert(
                            "Could not delete post."
                        );

                    }

                });

            }
        );

    });

</script>


</body>

</html>