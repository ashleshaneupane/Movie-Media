<?php
include '../includes/adminAuth.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    requireValidPost();

    $movieID = (int) ($_POST["movieID"] ?? 0);

    $stmt = $conn->prepare("DELETE FROM movie WHERE movieID = ?");
    $stmt->bind_param("i", $movieID);
    $stmt->execute();

    $_SESSION["flash"] = "Movie deleted.";
    header("Location: movies.php");
    exit;
}

$search = trim($_GET["q"] ?? "");
$like = "%" . $search . "%";

$stmt = $conn->prepare(
    "SELECT movieID, title, poster, releaseDate, genre
     FROM movie
     WHERE title LIKE ? OR genre LIKE ?
     ORDER BY title ASC"
);
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$movies = $stmt->get_result();

$pageTitle = "Movies";
include 'adminHeader.php';
?>

<h1>Movies</h1>

<div class="toolbar">
    <form method="get">
        <input type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search title or genre">
        <button type="submit">Search</button>
    </form>
    <a class="btn" href="movieForm.php">+ Add movie</a>
</div>

<table>
    <tr><th>Poster</th><th>Title</th><th>Genre</th><th>Release</th><th>Actions</th></tr>

    <?php while ($m = $movies->fetch_assoc()): ?>
        <tr>
            <td><img src="../<?php echo e($m["poster"]); ?>" alt=""></td>
            <td><?php echo e($m["title"]); ?></td>
            <td><?php echo e($m["genre"]); ?></td>
            <td><?php echo e($m["releaseDate"]); ?></td>
            <td>
                <a class="btn" href="movieForm.php?id=<?php echo (int) $m["movieID"]; ?>">Edit</a>
                <form method="post" class="inline" onsubmit="return confirm('Delete this movie?');">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="movieID" value="<?php echo (int) $m["movieID"]; ?>">
                    <button type="submit" class="danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'adminFooter.php'; ?>
