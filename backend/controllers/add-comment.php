<?php
session_start();
include_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$user_id = $_SESSION['user_id'];
if (isset($_POST['post_id']) && isset($_POST['comment_content'])) {
    $post_id = $_POST['post_id'];
    $comment_content = htmlspecialchars(trim($_POST['comment_content']), ENT_QUOTES, 'UTF-8');


    if (!empty($comment_content)) {
        $conn = new Database();
        $db = $conn->getConnection();

        $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (:post_id, :user_id, :content, NOW())");
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':content', $comment_content);

        if ($stmt->execute()) {
            $username = $_SESSION['username'];
            $created_at = date("Y-m-d H:i:s");

            echo json_encode([
                'success' => true,
                'comment' => [
                    'username' => $username,
                    'content' => $comment_content,
                    'created_at' => $created_at
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al agregar el comentario']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Comentario vacío']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}
