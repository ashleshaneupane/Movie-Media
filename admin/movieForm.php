<?php
include '../includes/adminAuth.php';

$movieID = (int) ($_GET["id"] ?? $_POST["movieID"] ?? 0);

$movie = [
    "title" => "", "poster" => "images/movies/", "releaseDate" => "",
    "genre" => "", "language" => "", "runtime" => "", "description" => "",
];

if ($movieID > 0) {
    $stmt = $conn->prepare("SELECT * FROM movie WHERE movieID = ?");
    $stmt->bind_param("i", $movieID);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();

    if (!$found) {
        $_SESSION["flash"] = "Movie not found.";
        header("Location: movies.php");
        exit;
    }
    $movie = $found;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    foreach (["title", "poster", "releaseDate", "genre", "language", "runtime", "description"] as $field) {
        $movie[$field] = trim($_POST[$field] ?? "");
    }

    // Poster must be a relative image path inside the site
    $posterOk = preg_match('#^images/[A-Za-z0-9_\-./ ]+\.(jpg|jpeg|png|webp)$#i', $movie["poster"])
        && strpos($movie["poster"], "..") === false;

    if ($movie["title"] === "") {
        $error = "Title is required.";
    } elseif (!$posterOk) {
        $error = "Poster must be an image path like images/movies/Inception.jpg.";
    } else {
        if ($movieID > 0) {
            $stmt = $conn->prepare(
                "UPDATE movie SET title = ?, poster = ?, releaseDate = ?, genre = ?,
                 language = ?, runtime = ?, description = ? WHERE movieID = ?"
            );
            $stmt->bind_param(
                "sssssssi",
                $movie["title"], $movie["poster"], $movie["releaseDate"], $movie["genre"],
                $movie["language"], $movie["runtime"], $movie["description"], $movieID
            );
            $_SESSION["flash"] = "Movie updated.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO movie (title, poster, releaseDate, genre, language, runtime, description)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "sssssss",
                $movie["title"], $movie["poster"], $movie["releaseDate"], $movie["genre"],
                $movie["language"], $movie["runtime"], $movie["description"]
            );
            $_SESSION["flash"] = "Movie added.";
        }

        $stmt->execute();
        header("Location: movies.php");
        exit;
    }
}

$pageTitle = $movieID > 0 ? "Edit movie" : "Add movie";
include 'adminHeader.php';
?>

<h1><?php echo e($pageTitle); ?></h1>

<?php if ($error !== ""): ?>
    <p class="flash error"><?php echo e($error); ?></p>
<?php endif; ?>

<form method="post" class="admin-form">
    <?php echo csrfField(); ?>
    <input type="hidden" name="movieID" value="<?php echo (int) $movieID; ?>">

    <label>Title <input type="text" name="title" required value="<?php echo e($movie["title"]); ?>"></label>
    <label>Poster path <input type="text" name="poster" required value="<?php echo e($movie["poster"]); ?>"></label>
    <label>Release date / year <input type="text" name="releaseDate" value="<?php echo e($movie["releaseDate"]); ?>"></label>
    <label>Genre <input type="text" name="genre" value="<?php echo e($movie["genre"]); ?>"></label>
    <label>Language <input type="text" name="language" value="<?php echo e($movie["language"]); ?>"></label>
    <label>Runtime <input type="text" name="runtime" value="<?php echo e($movie["runtime"]); ?>"></label>
    <label>Description <textarea name="description"><?php echo e($movie["description"]); ?></textarea></label>

    <div>
        <button type="submit">Save</button>
        <a class="btn" href="movies.php">Cancel</a>
    </div>
</form>

<?php include 'adminFooter.php'; ?>
