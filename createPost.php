<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Post - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="create-post-container">


        <!-- =========================
             PAGE TITLE
        ========================== -->

        <h1 id="page-title">
            Create Post
        </h1>



        <!-- =========================
             POST FORM
        ========================== -->

        <form
            class="create-post-form"
            id="createPostForm"
        >


            <!-- =========================
                 POST CONTENT
            ========================== -->

            <textarea
                id="postContent"
                name="postContent"
                placeholder="What's on your mind?"
            ></textarea>



            <!-- =========================
                 POST OPTIONS
            ========================== -->

            <div class="post-options">


                <!-- ADD MOVIE -->

                <button
                    type="button"
                    class="post-option"
                    id="addMovieBtn"
                >

                    
                    <span>Add Movie</span>

                </button>



                <!-- ADD PHOTO -->

                <button
                    type="button"
                    class="post-option"
                    id="addPhotoBtn"
                >

                    
                    <span>Add Photo</span>

                </button>



                <!-- WATCHED DATE -->

                <label
                    for="watchedDate"
                    class="post-option"
                >

                    
                    <span>Watched Date</span>

                    <input
                        type="date"
                        id="watchedDate"
                        name="watchedDate"
                    >

                </label>


            </div>



            <!-- =========================
                 MOVIE SELECTION
            ========================== -->

            <div
                class="movie-selection"
                id="movieSelection"
            >

                <input
                    type="text"
                    id="movieName"
                    name="movieName"
                    placeholder="Enter movie name..."
                >

            </div>



            <!-- =========================
                 PHOTO UPLOAD
            ========================== -->

            <div
                class="photo-selection"
                id="photoSelection"
            >

                <input
                    type="file"
                    id="photoInput"
                    name="photo"
                    accept="image/*"
                >

            </div>



            <!-- =========================
                 POST BUTTON
            ========================== -->

            <div class="post-submit">

                <button type="submit">
                    Post
                </button>

            </div>


        </form>


    </main>



    <!-- =========================
         JAVASCRIPT
    ========================== -->

    <script>


        /*
         * Get Elements
         */

        const addMovieBtn =
            document.getElementById("addMovieBtn");

        const addPhotoBtn =
            document.getElementById("addPhotoBtn");

        const movieSelection =
            document.getElementById("movieSelection");

        const photoSelection =
            document.getElementById("photoSelection");

        const createPostForm =
            document.getElementById("createPostForm");



        /*
         * ADD MOVIE
         */

        addMovieBtn.addEventListener(
            "click",
            function () {

                movieSelection.classList.toggle("active");

            }
        );



        /*
         * ADD PHOTO
         */

        addPhotoBtn.addEventListener(
            "click",
            function () {

                photoSelection.classList.toggle("active");

            }
        );



        /*
         * FORM SUBMISSION
         */

        createPostForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();


                /*
                 * Get post content
                 */

                const content =
                    document
                    .getElementById("postContent")
                    .value
                    .trim();



                /*
                 * Get movie
                 */

                const movie =
                    document
                    .getElementById("movieName")
                    .value
                    .trim();



                /*
                 * Check photo
                 */

                const photo =
                    document
                    .getElementById("photoInput")
                    .files.length;



                /*
                 * Get watched date
                 */

                const watchedDate =
                    document
                    .getElementById("watchedDate")
                    .value;



                /*
                 * Make sure something
                 * has been added.
                 */

                if (
                    content === "" &&
                    movie === "" &&
                    photo === 0
                ) {

                    alert(
                        "Please write something or add a movie/photo."
                    );

                    return;

                }



                /*
                 * Temporary frontend
                 * success message.
                 */

                alert(
                    "Post created successfully!"
                );



                /*
                 * Go back to Home
                 */

                window.location.href =
                    "home.php";

            }
        );

    </script>


</body>

</html>