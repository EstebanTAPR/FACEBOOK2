<?php
include_once '../config/database.php';

function createPost($userId, $content, $imagePath = '') {
    $conn = new Database();
    $db = $conn->getConnection();

    $stmt = $db->prepare("INSERT INTO posts (user_id, content, image, created_at) VALUES (:user_id, :content, :image, NOW())");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':image', $imagePath);

    if ($stmt->execute()) {
        $postId = $db->lastInsertId();

       
        $newPostStmt = $db->prepare("SELECT posts.id, users.username, posts.content, posts.image, posts.created_at 
                                     FROM posts 
                                     JOIN users ON posts.user_id = users.id 
                                     WHERE posts.id = :post_id");
        $newPostStmt->bindParam(':post_id', $postId);
        $newPostStmt->execute();

        return $newPostStmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}
