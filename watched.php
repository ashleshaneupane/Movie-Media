<?php
include 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Watched - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="watched-container">


        <!-- BACK BUTTON -->

        <a
            href="account.php"
            class="back-button"
        >
            ←
        </a>


        <!-- TITLE -->

        <h1 class="account-page-title">
            Watched
        </h1>


        <!-- MOVIE GRID -->

        <section class="watched-grid">


            <article class="watched-card">

                <div class="watched-poster">
                    1
                </div>

                <h2>
                    Interstellar
                </h2>

            </article>



            <article class="watched-card">

                <div class="watched-poster">
                    2
                </div>

                <h2>
                    Inception
                </h2>

            </article>



            <article class="watched-card">

                <div class="watched-poster">
                    3
                </div>

                <h2>
                    Avatar
                </h2>

            </article>



            <article class="watched-card">

                <div class="watched-poster">
                    4
                </div>

                <h2>
                    Oppenheimer
                </h2>

            </article>



            <article class="watched-card">

                <div class="watched-poster">
                    5
                </div>

                <h2>
                    The Batman
                </h2>

            </article>



            <article class="watched-card">

                <div class="watched-poster">
                    6
                </div>

                <h2>
                    Dune
                </h2>

            </article>



        </section>


    </main>


</body>

</html>