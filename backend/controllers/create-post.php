<?php
session_start();
include_once '../functions/post-functions.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$content = $_POST['post_content'] ?? '';
$imagePath = '';

if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
    $uploadDir = '../../frontend/images/';
    $fileName = basename($_FILES['post_image']['name']);
    $imagePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['post_image']['tmp_name'], $imagePath)) {
        $imagePath = 'images/' . $fileName; 
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al subir la imagen']);
        exit;
    }
}

// Llamar a la función createPost
$post = createPost($user_id, $content, $imagePath);

if ($post) {
    echo json_encode(['success' => true, 'post' => $post]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar la publicación']);
}
