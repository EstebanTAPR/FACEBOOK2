<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
include_once '../config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Verificar que se haya enviado el formulario con el post_id y el contenido del comentario
if (isset($_POST['post_id']) && isset($_POST['comment_content'])) {
    $post_id = $_POST['post_id'];
    $comment_content = trim($_POST['comment_content']);

    if (!empty($comment_content)) {
        // Crear una instancia de la conexión
        $conn = new Database();
        $db = $conn->getConnection();

        // Insertar el comentario en la base de datos
        $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, content, created_at) 
                              VALUES (:post_id, :user_id, :content, NOW())");
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':content', $comment_content);

        if ($stmt->execute()) {
            header("Location: ../../frontend/profile.php");
        } else {
            echo "Error al agregar el comentario.";
        }
    } else {
        echo "El comentario no puede estar vacío.";
    }
} else {
    echo "Datos incompletos.";
}
