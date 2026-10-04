<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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


    /*
        Keep the current profile picture
        unless a new one is uploaded.
    */

    $profilePicture = $user["profilePicture"];


    /*
        Handle profile picture upload
    */

    if (
        isset($_FILES["profilePhoto"]) &&
        $_FILES["profilePhoto"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        /*
            Check PHP Upload Errors
        */

        if ($_FILES["profilePhoto"]["error"] !== UPLOAD_ERR_OK) {

            switch ($_FILES["profilePhoto"]["error"]) {

                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    die("Error: The uploaded file exceeds the max allowed file size set by the server.");

                case UPLOAD_ERR_PARTIAL:
                    die("Error: The file was only partially uploaded. Please try again.");

                case UPLOAD_ERR_NO_TMP_DIR:
                    die("Error: Missing a temporary folder on the server.");

                case UPLOAD_ERR_CANT_WRITE:
                    die("Error: Failed to write file to disk. Check disk permissions.");

                default:
                    die("Error: Unknown file upload error code: " . $_FILES["profilePhoto"]["error"]);

            }

        }


        $file = $_FILES["profilePhoto"];

        $allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];


        /*
            Check file MIME type via finfo
        */

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detectedType = $finfo->file($file["tmp_name"]);

        if (!in_array($detectedType, $allowedTypes)) {

            die("Error: Invalid image type ('" . htmlspecialchars($detectedType) . "'). Please upload a JPG, PNG, or WebP image.");

        }


        /*
            Check file size
            Maximum: 5 MB
        */

        if ($file["size"] > 5 * 1024 * 1024) {

            die("Error: Profile picture must be smaller than 5 MB.");

        }


        /*
            Set target folder
            Note: Adjusted relative path from project root
        */

        $uploadFolder = "images/profile/";


        /*
            Create profile folder if it does not exist
        */

        if (!is_dir($uploadFolder)) {

            if (!mkdir($uploadFolder, 0777, true)) {

                die("Error: Could not create folder at target path: '" . $uploadFolder . "'. Please check parent directory permissions.");

            }

        }


        /*
            Get file extension
        */

        $extension = strtolower(
            pathinfo(
                $file["name"],
                PATHINFO_EXTENSION
            )
        );


        /*
            Create a unique filename
        */

        $fileName =
            "profile_" .
            $userID .
            "_" .
            time() .
            "." .
            $extension;


        $filePath =
            $uploadFolder .
            $fileName;


        /*
            Move uploaded file
        */
            


$filePath = __DIR__ . "/images/profile/" . $fileName;


        if (move_uploaded_file(
            $file["tmp_name"],
            $filePath
        )) {

           $profilePicture = "images/profile/" . $fileName;

        } else {

            $resolvedFolder = realpath($uploadFolder) ? realpath($uploadFolder) : "Folder does not exist";
            die("Error: move_uploaded_file failed.<br>Target directory: " . htmlspecialchars($resolvedFolder) . "<br>Attempted file path: " . htmlspecialchars($filePath));

        }

    }


    /*
        Update user information
    */

    $updateUser = $conn->prepare(
        "UPDATE Users
         SET name = ?,
             username = ?,
             bio = ?,
             favoriteGenre = ?,
             profilePicture = ?
         WHERE userID = ?"
    );

    $updateUser->bind_param(
        "sssssi",
        $fullName,
        $username,
        $bio,
        $favoriteGenre,
        $profilePicture,
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Profile - Movie Media</title>

    <link
        rel="stylesheet"
        href="css/index.css"
    >

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

                    <?php echo strtoupper(
                        substr($user["name"], 0, 1)
                    ); ?>

                <?php endif; ?>

            </div>


            <label
                for="profilePhoto"
                class="change-photo-btn"
            >
                Change Photo
            </label>

        </div>



        <!-- EDIT PROFILE FORM -->

        <form
            class="edit-profile-form"
            id="editProfileForm"
            method="POST"
            action="editProfile.php"
            enctype="multipart/form-data"
        >


            <!-- PROFILE PHOTO INPUT -->

            <input
                type="file"
                id="profilePhoto"
                name="profilePhoto"
                accept="image/jpeg,image/png,image/webp"
                hidden
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
                            <?php
                            echo (
                                $user["favoriteGenre"] === $genre
                            )
                            ? "selected"
                            : "";
                            ?>
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



    <script>

        const profilePhoto =
            document.getElementById("profilePhoto");

        const changePhotoBtn =
            document.querySelector(".change-photo-btn");


        changePhotoBtn.addEventListener(
            "click",
            function() {

                profilePhoto.click();

            }
        );


        /*
            Show selected image immediately
        */

        profilePhoto.addEventListener(
            "change",
            function() {

                if (this.files && this.files[0]) {

                    const reader =
                        new FileReader();

                    reader.onload =
                        function(event) {

                            const image =
                                document.querySelector(
                                    ".edit-profile-picture img"
                                );

                            if (image) {

                                image.src =
                                    event.target.result;

                            }
                            else {

                                const container =
                                    document.querySelector(
                                        ".edit-profile-picture"
                                    );

                                container.innerHTML =
                                    "<img src='" +
                                    event.target.result +
                                    "' alt='Profile Picture'>";

                            }

                        };

                    reader.readAsDataURL(
                        this.files[0]
                    );

                }

            }
        );

    </script>


</body>

</html>