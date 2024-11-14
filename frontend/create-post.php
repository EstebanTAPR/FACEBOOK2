<?php
session_start();
include('../backend/config/database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new Database();
$db = $conn->getConnection();
$user_id = $_SESSION['user_id'];
$content = $_POST['post_content'];

// Procesar imagen de la publicación
$post_image = null;
if ($_FILES['post_image']['name']) {
    $post_image = 'images/' . basename($_FILES['post_image']['name']);
    move_uploaded_file($_FILES['post_image']['tmp_name'], $post_image);
}

// Insertar publicación en la base de datos
$query = "INSERT INTO posts (user_id, content, image) VALUES (:user_id, :content, :image)";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':content', $content);
$stmt->bindParam(':image', $post_image);
$stmt->execute();

header("Location: profile.php");
?>
