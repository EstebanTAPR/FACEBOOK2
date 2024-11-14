<?php
// add-reaction.php
session_start();
include('../backend/config/database.php');

if (isset($_POST['post_id'], $_POST['reaction'], $_SESSION['user_id'])) {
    $post_id = $_POST['post_id'];
    $reaction_type = $_POST['reaction'];
    $user_id = $_SESSION['user_id'];

    $conn = new Database();
    $db = $conn->getConnection();

    $stmt = $db->prepare("INSERT INTO reactions (user_id, post_id, type, created_at) VALUES (:user_id, :post_id, :type, NOW())");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':type', $reaction_type);
    $stmt->execute();

    header("Location: profile.php");
} else {
    echo "Error: Datos incompletos.";
}
