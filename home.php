
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Home - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <?php include 'includes/header.php'; ?>


    <main class="home-layout">

        <!-- =========================
             LEFT SIDEBAR
        ========================== -->

        <aside class="left-sidebar">

            <div class="profile-card">

                <!-- Profile Picture -->
                <div class="profile-image">
                    <span>U</span>
                </div>

                <!-- User Information -->
                <h2>User Name</h2>

                <p class="username">@username</p>

                <p class="profile-bio">
                    Movie lover, escaping reality.
                </p>


                <!-- Profile Stats -->
                <div class="profile-stats">

                    <div class="profile-stat">
                        <strong>24</strong>
                        <span>Posts</span>
                    </div>

                    <div class="profile-stat">
                        <strong>81</strong>
                        <span>Watched</span>
                    </div>

                    <div class="profile-stat">
                        <strong>36</strong>
                        <span>Friends</span>
                    </div>

                </div>


                <!-- Movie Statistics -->
                <div class="movie-stats">

                    <div class="movie-stat-row">
                        <span>Watchlist</span>
                        <strong>17</strong>
                    </div>

                    <div class="movie-stat-row">
                        <span>Reviews</span>
                        <strong>29</strong>
                    </div>

                    <div class="movie-stat-row">
                        <span>Avg. Rating</span>
                        <strong>8.2</strong>
                    </div>

                </div>


                <!-- Favorite Genres -->
                <div class="profile-section">

                    <h3>Favorite Genres</h3>

                    <div class="genre-list">
                        <span>Thriller</span>
                        <span>Sci-Fi</span>
                        <span>Romance</span>
                        <span>Drama</span>
                    </div>

                </div>


                <!-- Top Movies -->
                <div class="profile-section">

                    <h3>My Top 5</h3>

                    <ol class="top-movies">
                        <li>Interstellar</li>
                        <li>Inception</li>
                        <li>Parasite</li>
                        <li>Arrival</li>
                        <li>La La Land</li>
                    </ol>

                </div>


                <!-- View Profile -->
                <a href="account.php" class="view-profile-btn">
                    View Profile
                </a>

            </div>

        </aside>



        <!-- =========================
             CENTER FEED
        ========================== -->

        <section class="home-feed">

            <!-- Create Post -->
            <div class="create-post-link">

    <a href="createPost.php">
        <span class="plus-icon">+</span>
        Create Post
    </a>

</div>



            <!-- =========================
                 POST 1
            ========================== -->

            <article class="post-card">

                <!-- Post Header -->
                <div class="post-header">

                    <div class="post-profile-image">
                        A
                    </div>

                    <div class="post-user-info">
                        <h3>Ash</h3>
                        <p>@ash</p>
                    </div>

                </div>


                <!-- Post Content -->
                <div class="post-content">

                    <p>
                        Finally watched Interstellar tonight. I genuinely don't know
                        how a movie can make me stare at the ceiling for twenty minutes
                        after it ends. 🚀
                    </p>


                    <!-- Movie Attachment -->
                    <div class="movie-attachment">

                        <div class="movie-poster-placeholder">
                            Movie Poster
                        </div>

                        <div class="movie-attachment-info">

                            <h3>Interstellar</h3>

                            <p>⭐ 8.7 / 10</p>

                            <p>
                                Watched: September 7, 2026
                            </p>

                        </div>

                    </div>

                </div>


                <!-- Post Actions -->
                <div class="post-actions">

                    <button type="button" class="like-button">
                        ☆ <span>24</span>
                    </button>

                    <button type="button">
                        ▤ <span>8</span>
                    </button>

                </div>

            </article>



            <!-- =========================
                 POST 2
            ========================== -->

            <article class="post-card">

                <div class="post-header">

                    <div class="post-profile-image">
                        M
                    </div>

                    <div class="post-user-info">
                        <h3>Movie Lover</h3>
                        <p>@movielover</p>
                    </div>

                </div>


                <div class="post-content">

                    <p>
                        Sometimes you just need a good movie and some peace.
                        This one was surprisingly beautiful.
                    </p>


                    <!-- Photo Attachment -->
                    <div class="photo-attachment">

                        <div class="photo-placeholder">
                            Your Photo
                        </div>

                    </div>


                    <div class="watched-info">
                        🎬 Watched a movie
                        <span>•</span>
                        September 6, 2026
                    </div>

                </div>


                <div class="post-actions">

                    <button type="button" class="like-button">
                        ☆ <span>18</span>
                    </button>

                    <button type="button">
                        ▤ <span>3</span>
                    </button>

                </div>

            </article>



            <!-- =========================
                 POST 3
            ========================== -->

            <article class="post-card">

                <div class="post-header">

                    <div class="post-profile-image">
                        S
                    </div>

                    <div class="post-user-info">
                        <h3>Sarah</h3>
                        <p>@sarahmovies</p>
                    </div>

                </div>


                <div class="post-content">

                    <p>
                        What a movie. The soundtrack alone deserves its own review.
                    </p>

                </div>


                <div class="post-actions">

                    <button type="button" class="like-button">
                        ☆ <span>31</span>
                    </button>

                    <button type="button">
                        ▤ <span>12</span>
                    </button>

                </div>

            </article>

        </section>



        <!-- =========================
             RIGHT SIDEBAR
        ========================== -->

        <aside class="right-sidebar">


            <!-- Search -->
            <div class="sidebar-search">

                <input type="text" placeholder="Search...">

                <button type="button">
                    ⌕
                </button>

            </div>



            <!-- Comments -->
            <div class="comments-card">

                <h2>Comments</h2>


                <div class="comment">

                    <div class="comment-profile">
                        A
                    </div>

                    <div class="comment-content">
                        <strong>@alex</strong>
                        <p>That ending 😭</p>
                    </div>

                </div>


                <div class="comment">

                    <div class="comment-profile">
                        M
                    </div>

                    <div class="comment-content">
                        <strong>@mike</strong>
                        <p>One of my favorites!</p>
                    </div>

                </div>


                <div class="comment">

                    <div class="comment-profile">
                        S
                    </div>

                    <div class="comment-content">
                        <strong>@sarah</strong>
                        <p>Need to watch this!</p>
                    </div>

                </div>


                <div class="comment">

                    <div class="comment-profile">
                        J
                    </div>

                    <div class="comment-content">
                        <strong>@jane</strong>
                        <p>The soundtrack is amazing.</p>
                    </div>

                </div>

            </div>

        </aside>

    </main>



    <script>

        /*
         * Temporary Like Interaction
         * Database functionality will be added later.
         */

        const likeButtons = document.querySelectorAll(".like-button");

        likeButtons.forEach(function(button) {

            button.addEventListener("click", function() {

                const count = button.querySelector("span");

                let likes = parseInt(count.textContent);


                if (button.classList.contains("liked")) {

                    likes--;

                    button.classList.remove("liked");

                    button.firstChild.textContent = "☆ ";

                } else {

                    likes++;

                    button.classList.add("liked");

                    button.firstChild.textContent = "★ ";

                }


                count.textContent = likes;

            });

        });

    </script>

</body>

</html>
