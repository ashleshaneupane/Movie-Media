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
                A
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
                    value="Ash Neupane"
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
                    value="ashmovies"
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
                >Movie lover 🎬</textarea>

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

                    <option value="Action">
                        Action
                    </option>

                    <option value="Adventure">
                        Adventure
                    </option>

                    <option value="Comedy">
                        Comedy
                    </option>

                    <option value="Drama">
                        Drama
                    </option>

                    <option value="Horror">
                        Horror
                    </option>

                    <option value="Romance">
                        Romance
                    </option>

                    <option value="Sci-Fi">
                        Sci-Fi
                    </option>

                    <option value="Thriller">
                        Thriller
                    </option>

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

        const editProfileForm =
            document.getElementById("editProfileForm");


        editProfileForm.addEventListener(
            "submit",
            function(event) {

                event.preventDefault();


                const fullName =
                    document.getElementById("fullName")
                    .value
                    .trim();


                const username =
                    document.getElementById("username")
                    .value
                    .trim();


                if (
                    fullName === "" ||
                    username === ""
                ) {

                    alert(
                        "Please fill in your name and username."
                    );

                    return;

                }


                alert(
                    "Profile updated successfully!"
                );


                window.location.href =
                    "account.php";

            }
        );


    </script>


</body>

</html>