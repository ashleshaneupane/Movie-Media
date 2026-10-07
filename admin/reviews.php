<?php

include '../includes/config.php';
include '../includes/adminAuth.php';

$pageTitle = "Reviews";

$currentPage = isset($_GET["page"])
    ? max(1, (int) $_GET["page"])
    : 1;

$perPage = 8;

$search = isset($_GET["q"])
    ? trim($_GET["q"])
    : "";

$spoilerFilter = isset($_GET["spoiler"])
    ? $_GET["spoiler"]
    : "";


/* =========================================================
   MARK REVIEW AS SPOILER
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    $reviewID = isset($_POST["reviewID"])
        ? (int) $_POST["reviewID"]
        : 0;

    if ($reviewID > 0) {

        $stmt = $conn->prepare(
            "UPDATE Review
             SET spoiler = 1
             WHERE reviewID = ?"
        );

        $stmt->bind_param(
            "i",
            $reviewID
        );

        $stmt->execute();

        $_SESSION["flash"] =
            "Review has been marked as a spoiler.";

        header("Location: reviews.php");

        exit;
    }
}


/* =========================================================
   BUILD FILTERS
========================================================= */

$where = [];
$params = [];
$types = "";


/* Search */

if ($search !== "") {

    $where[] = "
        (
            movie.title LIKE ?
            OR Users.name LIKE ?
            OR Users.username LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "sss";
}


/* Spoiler filter */

if ($spoilerFilter === "spoiler") {

    $where[] = "Review.spoiler = 1";

}

elseif ($spoilerFilter === "not_spoiler") {

    $where[] = "Review.spoiler = 0";

}


/* WHERE clause */

$whereSQL = "";

if (!empty($where)) {

    $whereSQL =
        "WHERE " . implode(" AND ", $where);

}


/* =========================================================
   COUNT REVIEWS
========================================================= */

$countSQL = "
    SELECT COUNT(*) AS total
    FROM Review
    INNER JOIN movie
        ON Review.movieID = movie.movieID
    INNER JOIN Users
        ON Review.userID = Users.userID
    $whereSQL
";

$countStmt = $conn->prepare($countSQL);

if (!empty($params)) {

    $countStmt->bind_param(
        $types,
        ...$params
    );

}

$countStmt->execute();

$countResult = $countStmt->get_result();

$totalReviews =
    (int) $countResult
        ->fetch_assoc()["total"];


/* =========================================================
   PAGINATION
========================================================= */

$totalPages = max(
    1,
    (int) ceil(
        $totalReviews / $perPage
    )
);

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset =
    ($currentPage - 1) * $perPage;


/* =========================================================
   GET REVIEWS
========================================================= */

$sql = "
    SELECT
        Review.reviewID,
        Review.rating,
        Review.reviewText,
        Review.spoiler,
        Review.reviewDate,

        movie.movieID,
        movie.title,
        movie.poster,

        Users.userID,
        Users.name,
        Users.username,
        Users.profilePicture

    FROM Review

    INNER JOIN movie
        ON Review.movieID = movie.movieID

    INNER JOIN Users
        ON Review.userID = Users.userID

    $whereSQL

    ORDER BY Review.reviewDate DESC

    LIMIT ? OFFSET ?
";


$stmt = $conn->prepare($sql);


/*
 * Add pagination parameters
 */

$finalParams = $params;
$finalParams[] = $perPage;
$finalParams[] = $offset;

$finalTypes =
    $types . "ii";


$stmt->bind_param(
    $finalTypes,
    ...$finalParams
);

$stmt->execute();

$reviewResult =
    $stmt->get_result();


/* =========================================================
   SPOILER COUNTS
========================================================= */

$spoilerCountResult =
    $conn->query(
        "SELECT
            COUNT(*) AS total,
            SUM(spoiler = 1) AS spoilers,
            SUM(spoiler = 0) AS notSpoilers
         FROM Review"
    );

$spoilerCounts =
    $spoilerCountResult->fetch_assoc();

$totalAllReviews =
    (int) $spoilerCounts["total"];

$totalSpoilers =
    (int) $spoilerCounts["spoilers"];

$totalNotSpoilers =
    (int) $spoilerCounts["notSpoilers"];

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
        <?php echo e($pageTitle); ?> - Movie Media Admin
    </title>

    <link
        rel="stylesheet"
        href="admin.css"
    >

</head>


<body>


<?php include 'adminHeader.php'; ?>


<main class="admin-main">


    <!-- =====================================================
         PAGE HEADING
    ====================================================== -->

    <div class="page-heading">

        <h1>
            Reviews
        </h1>

        <p class="page-subtitle">
            Review user feedback and manage spoiler flags.
        </p>

    </div>


    <!-- =====================================================
         REVIEW STATS
    ====================================================== -->

    <section class="review-stats">


        <div class="review-stat">

            <span>
                Total Reviews
            </span>

            <strong>
                <?php echo $totalAllReviews; ?>
            </strong>

        </div>


        <div class="review-stat">

            <span>
                Spoiler Reviews
            </span>

            <strong>
                <?php echo $totalSpoilers; ?>
            </strong>

        </div>


        <div class="review-stat">

            <span>
                Unmarked
            </span>

            <strong>
                <?php echo $totalNotSpoilers; ?>
            </strong>

        </div>


    </section>


    <!-- =====================================================
         TOOLBAR
    ====================================================== -->

    <form
        method="GET"
        class="reviews-toolbar"
    >


        <input
            type="search"
            name="q"
            value="<?php echo e($search); ?>"
            placeholder="Search movie or reviewer..."
        >


        <select name="spoiler">

            <option
                value=""
                <?php echo $spoilerFilter === ""
                    ? "selected"
                    : ""; ?>
            >
                All Reviews
            </option>

            <option
                value="spoiler"
                <?php echo $spoilerFilter === "spoiler"
                    ? "selected"
                    : ""; ?>
            >
                Spoilers
            </option>

            <option
                value="not_spoiler"
                <?php echo $spoilerFilter === "not_spoiler"
                    ? "selected"
                    : ""; ?>
            >
                Not Marked
            </option>

        </select>


        <button
            type="submit"
            class="btn"
        >
            Filter
        </button>


        <?php if ($search !== "" || $spoilerFilter !== ""): ?>

            <a
                href="reviews.php"
                class="clear-filter"
            >
                Clear
            </a>

        <?php endif; ?>


    </form>


    <!-- =====================================================
         REVIEWS
    ====================================================== -->

    <?php if ($reviewResult->num_rows === 0): ?>


        <div class="review-empty">

            <h2>
                No reviews found
            </h2>

            <p>
                Try changing your search or filter.
            </p>

        </div>


    <?php else: ?>


        <section class="admin-review-list">


            <?php while ($review = $reviewResult->fetch_assoc()): ?>


                <article class="admin-review-card">


                    <!-- HEADER -->

                    <div class="admin-review-header">


                        <div class="admin-review-user">


                            <?php if (!empty($review["profilePicture"])): ?>

                                <img
                                    src="../<?php echo e($review["profilePicture"]); ?>"
                                    alt="<?php echo e($review["name"]); ?>"
                                    class="admin-review-avatar"
                                >

                            <?php else: ?>

                                <div class="admin-review-avatar-placeholder">

                                    <?php
                                    echo strtoupper(
                                        substr(
                                            $review["name"],
                                            0,
                                            1
                                        )
                                    );
                                    ?>

                                </div>

                            <?php endif; ?>


                            <div>

                                <strong>
                                    <?php echo e($review["name"]); ?>
                                </strong>

                                <small>
                                    @<?php echo e($review["username"]); ?>
                                </small>

                            </div>


                        </div>


                        <?php if ((int) $review["spoiler"] === 1): ?>

                            <span class="review-status spoiler">
                                ⚠ Spoiler
                            </span>

                        <?php else: ?>

                            <span class="review-status">
                                No Spoiler Flag
                            </span>

                        <?php endif; ?>


                    </div>


                    <!-- MOVIE -->

                    <div class="admin-review-movie">


                        <?php if (!empty($review["poster"])): ?>

                            <img
                                src="../<?php echo e($review["poster"]); ?>"
                                alt="<?php echo e($review["title"]); ?>"
                            >

                        <?php else: ?>

                            <div class="admin-review-poster-placeholder">
                                🎬
                            </div>

                        <?php endif; ?>


                        <div>

                            <strong>
                                <?php echo e($review["title"]); ?>
                            </strong>

                            <small>
                                Movie Review
                            </small>

                        </div>


                    </div>


                    <!-- RATING -->

                    <div class="admin-review-rating">

                        <?php

                        $rating =
                            (float) $review["rating"];

                        echo number_format(
                            $rating,
                            1
                        );

                        ?>

                        <span>
                            / 10
                        </span>

                    </div>


                    <!-- REVIEW TEXT -->

                    <?php if (!empty($review["reviewText"])): ?>

                        <p class="admin-review-text">

                            <?php
                            echo nl2br(
                                e(
                                    $review["reviewText"]
                                )
                            );
                            ?>

                        </p>

                    <?php else: ?>

                        <p class="admin-review-no-text">
                            No written review.
                        </p>

                    <?php endif; ?>


                    <!-- DATE -->

                    <div class="admin-review-date">

                        <?php
                        echo date(
                            "M d, Y",
                            strtotime(
                                $review["reviewDate"]
                            )
                        );
                        ?>

                    </div>


                    <!-- ACTION -->

                    <div class="admin-review-actions">


                        <?php if ((int) $review["spoiler"] === 0): ?>

                            <form
                                method="POST"
                                class="inline"
                            >

                                <?php echo csrfField(); ?>

                                <input
                                    type="hidden"
                                    name="reviewID"
                                    value="<?php echo (int) $review["reviewID"]; ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn spoiler-btn"
                                >
                                    Mark as Spoiler
                                </button>

                            </form>


                        <?php else: ?>

                            <span class="spoiler-marked">
                                ⚠ Spoiler Marked
                            </span>

                        <?php endif; ?>


                    </div>


                </article>


            <?php endwhile; ?>


        </section>


        <!-- =================================================
             PAGINATION
        ================================================== -->

        <?php if ($totalPages > 1): ?>


            <div class="pagination">


                <?php if ($currentPage > 1): ?>

                    <a
                        href="?<?php
                            echo http_build_query([
                                "q" => $search,
                                "spoiler" => $spoilerFilter,
                                "page" => $currentPage - 1
                            ]);
                        ?>"
                    >
                        ←
                    </a>

                <?php endif; ?>


                <?php for (
                    $i = 1;
                    $i <= $totalPages;
                    $i++
                ): ?>

                    <a
                        href="?<?php
                            echo http_build_query([
                                "q" => $search,
                                "spoiler" => $spoilerFilter,
                                "page" => $i
                            ]);
                        ?>"
                        class="<?php echo $i === $currentPage
                            ? "active"
                            : ""; ?>"
                    >
                        <?php echo $i; ?>
                    </a>

                <?php endfor; ?>


                <?php if ($currentPage < $totalPages): ?>

                    <a
                        href="?<?php
                            echo http_build_query([
                                "q" => $search,
                                "spoiler" => $spoilerFilter,
                                "page" => $currentPage + 1
                            ]);
                        ?>"
                    >
                        →
                    </a>

                <?php endif; ?>


            </div>


        <?php endif; ?>


    <?php endif; ?>


</main>


</body>

</html>