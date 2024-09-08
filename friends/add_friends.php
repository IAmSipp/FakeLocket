<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../profile/register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $friend_username = $_POST['friend_username'];

    // FETCH FRIEND USER BY USERNAME
    $user_query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("s", $friend_username);
    $stmt->execute();
    $friend = $stmt->get_result()->fetch_assoc();

    if ($friend) {
        $friend_id = $friend['id'];
        $user_id = $_SESSION['user_id'];

        // CHECK IF THEY ARE ALREADY FRIENDS
        $check_friend_query = "SELECT * FROM user_friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
        $stmt = $conn->prepare($check_friend_query);
        $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
        $stmt->execute();
        $already_friends = $stmt->get_result()->num_rows > 0;

        if (!$already_friends) {
            // ADD FRIEND RELATIONSHIP FOR BOTH USERS
            $insert_friend_query = "INSERT INTO user_friends (user_id, friend_id) VALUES (?, ?), (?, ?)";
            $stmt = $conn->prepare($insert_friend_query);
            $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
            $stmt->execute();

            $_SESSION['message'] = "You are now friends with {$friend_username}!";
        } else {
            $_SESSION['message'] = "You are already friends with this user.";
        }
    } else {
        $_SESSION['message'] = "User not found.";
    }

    header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Friend</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body class="bg-dark text-light">

    <div class="container mt-5">
        <div class="card bg-secondary">
            <div class="card-body">
                <h4 class="card-title text-center">Add a Friend</h4>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?php echo $_SESSION['message'];
                                                    unset($_SESSION['message']); ?></div>
                <?php endif; ?>
                <form method="POST" action="add_friends.php">
                    <div class="mb-3">
                        <label for="friend_username" class="form-label">Friend's Username</label>
                        <input type="text" class="form-control" id="friend_username" name="friend_username" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Friend</button>
                </form>
                <a href="../index.php" class="btn btn-warning mt-3 w-100">Back to Home</a>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>