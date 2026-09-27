<?php
include 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reviews - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="reviews-container">


        <!-- BACK BUTTON -->

        <a
            href="account.php"
            class="back-button"
        >
            ←
        </a>


        <!-- TITLE -->

        <h1 class="account-page-title">
            Reviews
        </h1>


        <!-- REVIEWS -->

        <section class="reviews-grid">


            <!-- REVIEW 1 -->

            <article class="review-card">


                <div class="review-poster">
                    1
                </div>


                <h2>
                    Interstellar
                </h2>


                <div class="rating">
                    ★★★★★
                </div>


                <p class="review-text">
                    One of the best sci-fi movies I have ever watched.
                </p>


                <div class="review-actions">

                    <button
                        type="button"
                        class="review-edit-btn"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="review-delete-btn"
                    >
                        Delete
                    </button>

                </div>


            </article>



            <!-- REVIEW 2 -->

            <article class="review-card">


                <div class="review-poster">
                    2
                </div>


                <h2>
                    Inception
                </h2>


                <div class="rating">
                    ★★★★☆
                </div>


                <p class="review-text">
                    A mind-bending story with amazing visuals.
                </p>


                <div class="review-actions">

                    <button
                        type="button"
                        class="review-edit-btn"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="review-delete-btn"
                    >
                        Delete
                    </button>

                </div>


            </article>



            <!-- REVIEW 3 -->

            <article class="review-card">


                <div class="review-poster">
                    3
                </div>


                <h2>
                    Oppenheimer
                </h2>


                <div class="rating">
                    ★★★★★
                </div>


                <p class="review-text">
                    Brilliant performances and storytelling.
                </p>


                <div class="review-actions">

                    <button
                        type="button"
                        class="review-edit-btn"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="review-delete-btn"
                    >
                        Delete
                    </button>

                </div>


            </article>


        </section>


    </main>



    <script>

        const deleteButtons =
            document.querySelectorAll(
                ".review-delete-btn"
            );


        deleteButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    const review =
                        button.closest(".review-card");


                    review.remove();

                }
            );

        });


        const editButtons =
            document.querySelectorAll(
                ".review-edit-btn"
            );


        editButtons.forEach(function(button) {

            button.addEventListener(
                "click",
                function() {

                    alert(
                        "Review editing will be available soon."
                    );

                }
            );

        });

    </script>


</body>

</html>