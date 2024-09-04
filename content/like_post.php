<?php
session_start();
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    $check_like_query = "SELECT * FROM content_likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($check_like_query);
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $like_exists = $stmt->get_result()->num_rows > 0;

    if ($like_exists) {
        $delete_like_query = "DELETE FROM content_likes WHERE user_id = ? AND post_id = ?";
        $stmt = $conn->prepare($delete_like_query);
        $stmt->bind_param("ii", $user_id, $post_id);
        if ($stmt->execute()) {
            $update_post_query = "UPDATE contents SET likes = likes - 1 WHERE id = ?";
            $stmt = $conn->prepare($update_post_query);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
        }
    } else {
        $insert_like_query = "INSERT INTO content_likes (user_id, post_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_like_query);
        $stmt->bind_param("ii", $user_id, $post_id);
        if ($stmt->execute()) {
            $update_post_query = "UPDATE contents SET likes = likes + 1 WHERE id = ?";
            $stmt = $conn->prepare($update_post_query);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
        }
    }

    // REDIRECT BACK TO POSTS PAGE
    header("Location: ../index.php");
    exit();
}
