<?php
include 'includes/auth.php';
include 'includes/config.php';

$currentUserID = (int)$_SESSION["userID"];

/* GET ACCEPTED FRIENDS */
$friendQuery = $conn->prepare(
    "SELECT
        Users.userID,
        Users.name,
        Users.username,
        Users.profilePicture
    FROM FriendRequest
    INNER JOIN Users
        ON (
            Users.userID = FriendRequest.senderID
            AND FriendRequest.receiverID = ?
        )
        OR (
            Users.userID = FriendRequest.receiverID
            AND FriendRequest.senderID = ?
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

</head>


<body>

    <?php include 'includes/header.php'; ?>


    <main class="connect-container">

        <div class="connect-card">

            <p class="connect-label">
                WELCOME TO MOVIE MEDIA
            </p>


            <div class="connect-icon">
                <img id="icon-user" src="images/Users.png" alt="users">
            </div>


            <?php if ($friends->num_rows > 0): ?>

                <h1>
                    Your Friends
                </h1>

                <p class="connect-description">
                    Stay connected with your movie friends
                    and discover more people who love movies.
                </p>


                <div class="friends-list">

                    <?php while ($friend = $friends->fetch_assoc()): ?>

                        <div class="friend-item">

                            <div class="friend-item-profile">

                                <?php if (!empty($friend["profilePicture"])): ?>

                                    <img
                                        src="<?php echo htmlspecialchars($friend["profilePicture"]); ?>"
                                        alt="<?php echo htmlspecialchars($friend["name"]); ?>"
                                    >

                                <?php else: ?>

                                    <div class="friend-item-placeholder">
                                        <?php echo strtoupper(substr($friend["name"], 0, 1)); ?>
                                    </div>

                                <?php endif; ?>

                            </div>


                            <div class="friend-item-info">

                                <strong>
                                    <?php echo htmlspecialchars($friend["name"]); ?>
                                </strong>

                                <span>
                                    @<?php echo htmlspecialchars($friend["username"]); ?>
                                </span>

                            </div>

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

                <h1>
                    Find Friends
                </h1>


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


            <a
                href="home.php"
                class="skip-link"
            >
                Back to Home
            </a>

        </div>

    </main>


</body>

</html>