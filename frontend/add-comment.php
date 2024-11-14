<?php
// add-comment.php
session_start();
include('../backend/config/database.php');

if (isset($_POST['post_id'], $_POST['comment_content'], $_SESSION['user_id'])) {
    $post_id = $_POST['post_id'];
    $comment_content = $_POST['comment_content'];
    $user_id = $_SESSION['user_id'];

    $conn = new Database();
    $db = $conn->getConnection();

    $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (:post_id, :user_id, :content, NOW())");
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':content', $comment_content);
    $stmt->execute();

    header("Location: profile.php");
} else {
    echo "Error: Datos incompletos.";
}
