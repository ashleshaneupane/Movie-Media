<?php

include 'includes/auth.php';
include 'includes/config.php';

$userID = $_SESSION["userID"];

$userQuery = $conn->prepare(
    "SELECT name, username, email, profilePicture, bio, favoriteGenre
     FROM Users
     WHERE userID = ?"
);

$userQuery->bind_param("i", $userID);
$userQuery->execute();

$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["fullName"]);
    $username = trim($_POST["username"]);
    $bio = trim($_POST["bio"]);
    $favoriteGenre = trim($_POST["favoriteGenre"]);

    $updateUser = $conn->prepare(
        "UPDATE Users
         SET name = ?, username = ?, bio = ?, favoriteGenre = ?
         WHERE userID = ?"
    );

    $updateUser->bind_param(
        "ssssi",
        $fullName,
        $username,
        $bio,
        $favoriteGenre,
        $userID
    );

    if ($updateUser->execute()) {
        header("Location: account.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Profile - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="edit-profile-container">


        <!-- BACK BUTTON -->

        <a
            href="account.php"
            class="back-button"
        >
            ←
        </a>



        <!-- PAGE TITLE -->

        <h1 class="edit-profile-title">
            Edit Profile
        </h1>



        <!-- PROFILE PHOTO -->

        <div class="edit-photo-section">

            <div class="edit-profile-picture">
    <?php if (!empty($user["profilePicture"])): ?>
        <img
            src="<?php echo htmlspecialchars($user["profilePicture"]); ?>"
            alt="Profile Picture"
        >
    <?php else: ?>
        <?php echo strtoupper(substr($user["name"], 0, 1)); ?>
    <?php endif; ?>
</div>


            <label
                for="profilePhoto"
                class="change-photo-btn"
            >
                Change Photo
            </label>


            <input
                type="file"
                id="profilePhoto"
                accept="image/*"
            >

        </div>



        <!-- EDIT PROFILE FORM -->

   <form
    class="edit-profile-form"
    id="editProfileForm"
    method="POST"
    action="editProfile.php"
>


            <!-- FULL NAME -->

            <div class="profile-field">

                <label for="fullName">
                    Full Name
                </label>

            <input
    type="text"
    id="fullName"
    name="fullName"
    value="<?php echo htmlspecialchars($user["name"]); ?>"
    required
>

            </div>



            <!-- USERNAME -->

            <div class="profile-field">

                <label for="username">
                    Username
                </label>

              <input
    type="text"
    id="username"
    name="username"
    value="<?php echo htmlspecialchars($user["username"]); ?>"
    required
>
            </div>



            <!-- BIO -->

            <div class="profile-field">

                <label for="bio">
                    Bio
                </label>

                <textarea
    id="bio"
    name="bio"
    placeholder="Tell us about yourself..."
><?php echo htmlspecialchars($user["bio"] ?? ""); ?></textarea>
            </div>



            <!-- FAVORITE GENRE -->

            <div class="profile-field">

                <label for="favoriteGenre">
                    Favorite Genre
                </label>

                <select
    id="favoriteGenre"
    name="favoriteGenre"
>
    <option value="">
        Select Genre
    </option>

    <?php
    $genres = [
        "Action",
        "Adventure",
        "Comedy",
        "Drama",
        "Horror",
        "Romance",
        "Sci-Fi",
        "Thriller"
    ];

    foreach ($genres as $genre):
    ?>

        <option
            value="<?php echo htmlspecialchars($genre); ?>"
            <?php echo ($user["favoriteGenre"] === $genre) ? "selected" : ""; ?>
        >
            <?php echo htmlspecialchars($genre); ?>
        </option>

    <?php endforeach; ?>

</select>
            </div>



            <!-- SAVE -->

            <button
                type="submit"
                class="save-profile-btn"
            >
                SAVE
            </button>


        </form>


    </main>



    

</body>

</html>