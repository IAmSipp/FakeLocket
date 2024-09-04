<?php
session_start();
require_once 'database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: profile/register.php");
    exit();
}

$posts_query = "SELECT contents.*, users.username FROM contents JOIN users ON contents.owner_id = users.id ORDER BY created_at DESC";
$result = $conn->query($posts_query);

$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="bg-dark text-light">
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><b>BFriend</b></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Profile: <b><?php echo htmlspecialchars($user['username']); ?></b></span>
                    </li>
                    <li class="nav-item">
                        <a href="./content/create_post.php" class="nav-link text-light btn-primary rounded mx-2">Post New Image!</a>
                    </li>
                    <li class="nav-item">
                        <a href="profile/login.php" class="nav-link btn-danger rounded mx-2">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container mt-4">
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($post = $result->fetch_assoc()): ?>
                    <?php
                    $like_check_query = "SELECT * FROM content_likes WHERE user_id = ? AND post_id = ?";
                    $stmt = $conn->prepare($like_check_query);
                    $stmt->bind_param("ii", $_SESSION['user_id'], $post['id']);
                    $stmt->execute();
                    $user_liked = $stmt->get_result()->num_rows > 0;

                    $image_path = str_replace('../', '', $post['image']);
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-secondary text-light">
                            <div class="card-body">
                                <p><strong><?php echo htmlspecialchars($post['username']); ?></strong></p>
                                <?php if ($post['image']): ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Post Image" class="card-img-top">
                                <?php endif; ?>
                                <p class="text-muted">Posted on: <?php echo $post['created_at']; ?></p>

                                <form method="POST" action="./content/like_post.php" class="d-inline">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-<?php echo $user_liked ? 'danger' : 'primary'; ?> btn-sm">
                                        <?php echo $user_liked ? 'Unlike' : 'Like'; ?> (<?php echo $post['likes']; ?>)
                                    </button>
                                </form>

                                <?php if ($_SESSION['user_id'] == $post['owner_id']): ?>
                                    <a href="./content/edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="./content/delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card text-center p-4 bg-secondary text-light">
                        <div class="card-body">
                            <h5 class="card-title">No posts yet ;-;</h5>
                            <a href="content/create_post.php" class="btn btn-primary">Create New Post</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>