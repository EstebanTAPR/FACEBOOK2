<?php
include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../functions/user-functions.php';

function updateProfile($userId, $data) {
    global $db;
    $conn = new Database();
    $db = $conn->getConnection();
    // Comenzar la consulta SQL para actualizar los datos del usuario
    $sql = "UPDATE users SET 
        bio = :bio,
        education = :education,
        city = :city,
        hometown = :hometown";

    // Solo actualizar las imágenes si fueron cargadas
    if (!empty($data['profile_picture'])) {
        $sql .= ", profile_picture = :profile_picture";
    }
    if (!empty($data['cover_picture'])) {
        $sql .= ", cover_picture = :cover_picture";
    }

    $sql .= " WHERE id = :user_id";

    // Preparar la consulta
    $query = $db->prepare($sql);
    $query->bindParam(':bio', $data['bio']);
    $query->bindParam(':education', $data['education']);
    $query->bindParam(':city', $data['city']);
    $query->bindParam(':hometown', $data['hometown']);
    $query->bindParam(':user_id', $userId);

    // Solo vincular los parámetros de imagen si existen
    if (!empty($data['profile_picture'])) {
        $query->bindParam(':profile_picture', $data['profile_picture']);
    }
    if (!empty($data['cover_picture'])) {
        $query->bindParam(':cover_picture', $data['cover_picture']);
    }

    // Ejecutar la consulta
    $query->execute();
}
