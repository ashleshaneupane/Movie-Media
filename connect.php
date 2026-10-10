
<?php
include 'includes/auth.php';
include 'includes/config.php';

$currentUserID = (int) $_SESSION["userID"];

/* GET ACCEPTED FRIENDS */

$friendQuery = $conn->prepare(
    "SELECT DISTINCT
        Users.userID,
        Users.name,
        Users.username,
        Users.profilePicture
    FROM FriendRequest
    INNER JOIN Users
        ON (
            (
                Users.userID = FriendRequest.senderID
                AND FriendRequest.receiverID = ?
            )
            OR
            (
                Users.userID = FriendRequest.receiverID
                AND FriendRequest.senderID = ?
            )
        )
    WHERE FriendRequest.status = 'accepted'
    AND Users.userID != ?
    AND Users.role != 'admin'
    ORDER BY Users.name ASC"
);

$friendQuery->bind_param(
    "iii",
    $currentUserID,
    $currentUserID,
    $currentUserID
);

$friendQuery->execute();
$friends = $friendQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connect - Movie Media</title>

    <link rel="stylesheet" href="css/index.css">

    <style>
        /* Clickable friend profile */

        .friend-profile-link {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
            color: inherit;
            text-decoration: none;
            border-radius: 10px;
        }

        .friend-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .friend-item-profile {
            flex-shrink: 0;
        }

        .friend-item-profile img,
        .friend-item-placeholder {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .friend-item-info {
            min-width: 0;
        }

        .friend-item-info strong,
        .friend-item-info span {
            display: block;
            overflow-wrap: anywhere;
        }

        .friend-profile-link:hover .friend-item-info strong {
            color: #d8b56d;
        }

        .friend-profile-link:hover .friend-item-profile img,
        .friend-profile-link:hover .friend-item-placeholder {
            outline: 2px solid #806b45;
            outline-offset: 2px;
        }

        .friend-profile-link:focus-visible {
            outline: 2px solid #d8b56d;
            outline-offset: 4px;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <main class="connect-container">

        <div class="connect-card">

            <p class="connect-label">
                WELCOME TO MOVIE MEDIA
            </p>

            <div class="connect-icon">
                <img
                    id="icon-user"
                    src="images/Users.png"
                    alt="users"
                >
            </div>

            <?php if ($friends->num_rows > 0): ?>

                <h1>Your Friends</h1>

                <p class="connect-description">
                    Stay connected with your movie friends
                    and discover more people who love movies.
                </p>

                <div class="friends-list">

                    <?php while ($friend = $friends->fetch_assoc()): ?>

                        <div class="friend-item">

                            <a
                                class="friend-profile-link"
                               href="profile.php?user=<?php echo (int) $friend["userID"]; ?>"
                                aria-label="View <?php
                                    echo htmlspecialchars(
                                        $friend["name"],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>'s public profile"
                            >

                                <div class="friend-item-profile">

                                    <?php if (!empty($friend["profilePicture"])): ?>

                                        <img
                                            src="<?php
                                                echo htmlspecialchars(
                                                    $friend["profilePicture"],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>"
                                            alt=""
                                        >

                                    <?php else: ?>

                                        <div class="friend-item-placeholder">
                                            <?php
                                                echo htmlspecialchars(
                                                    strtoupper(
                                                        substr($friend["name"], 0, 1)
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                            ?>
                                        </div>

                                    <?php endif; ?>

                                </div>

                                <div class="friend-item-info">

                                    <strong>
                                        <?php
                                            echo htmlspecialchars(
                                                $friend["name"],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>
                                    </strong>

                                    <span>
                                        @<?php
                                            echo htmlspecialchars(
                                                $friend["username"],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>
                                    </span>

                                </div>

                            </a>

                        </div>

                    <?php endwhile; ?>

                </div>

                <a
                    href="findFriends.php"
                    class="find-friends-btn"
                >
                    Find More Friends
                </a>

            <?php else: ?>

                <h1>Find Friends</h1>

                <p class="connect-description">
                    Connect with other movie lovers,
                    discover new people and share
                    your favorite movies together.
                </p>

                <a
                    href="findFriends.php"
                    class="find-friends-btn"
                >
                    Find Friends
                </a>

            <?php endif; ?>

            <a href="home.php" class="skip-link">
                Back to Home
            </a>

        </div>

    </main>

</body>
</html>