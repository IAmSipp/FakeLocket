<?php
session_start();
unset($_SESSION['success_message']);
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_post'])) {
    $owner_id = $_SESSION['user_id'];
    $image = '';

    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['error_message'] = "You must select an image to upload.";
        header('Location: create_post.php');
        exit();
    }

    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $image_path = '../uploads/' . $image_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image = $image_path;
            } else {
                $_SESSION['error_message'] = "Failed to upload image.";
                header('Location: create_post.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Invalid image type. Please upload a JPG, PNG, or GIF.";
            header('Location: create_post.php');
            exit();
        }
    }

    $insert_query = "INSERT INTO contents (owner_id, image) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("is", $owner_id, $image);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Post created successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();

    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2>Post New Image</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <form action="create_post.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="image" class="form-label">Upload Image</label>
                <input type="file" name="image" id="image" class="form-control" required>
            </div>
            <button type="submit" name="create_post" class="btn btn-secondary">Post</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>