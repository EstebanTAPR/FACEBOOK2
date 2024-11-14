<?php
include_once '../config/database.php';

session_start();

$userId = $_SESSION['user_id'];
$postId = $_POST['post_id'];
$reactionType = $_POST['reaction_type'];

$conn = new Database();
$db = $conn->getConnection();

// Primero, verifica si el usuario ya ha reaccionado a esta publicación
$checkReactionStmt = $db->prepare("SELECT id FROM reactions WHERE user_id = :user_id AND post_id = :post_id");
$checkReactionStmt->bindParam(':user_id', $userId);
$checkReactionStmt->bindParam(':post_id', $postId);
$checkReactionStmt->execute();
$existingReaction = $checkReactionStmt->fetch(PDO::FETCH_ASSOC);

if ($existingReaction) {
    // Si ya existe, actualiza la reacción
    $updateStmt = $db->prepare("UPDATE reactions SET reaction_type = :reaction_type WHERE id = :reaction_id");
    $updateStmt->bindParam(':reaction_type', $reactionType);
    $updateStmt->bindParam(':reaction_id', $existingReaction['id']);
    $updateStmt->execute();
} else {
    // Si no existe, inserta una nueva reacción
    $insertStmt = $db->prepare("INSERT INTO reactions (user_id, post_id, reaction_type) VALUES (:user_id, :post_id, :reaction_type)");
    $insertStmt->bindParam(':user_id', $userId);
    $insertStmt->bindParam(':post_id', $postId);
    $insertStmt->bindParam(':reaction_type', $reactionType);
    $insertStmt->execute();
}

// Obtener el conteo actualizado de reacciones
$reactionCountsStmt = $db->prepare("SELECT reaction_type, COUNT(*) as count FROM reactions WHERE post_id = :post_id GROUP BY reaction_type");
$reactionCountsStmt->bindParam(':post_id', $postId);
$reactionCountsStmt->execute();
$reactions = $reactionCountsStmt->fetchAll(PDO::FETCH_ASSOC);

$response = ['success' => true, 'reactions' => ['like' => 0, 'love' => 0, 'haha' => 0]];

foreach ($reactions as $reaction) {
    $response['reactions'][$reaction['reaction_type']] = $reaction['count'];
}

// Enviar respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
