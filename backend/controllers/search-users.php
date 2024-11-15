<?php
include_once '../config/database.php';

$query = $_GET['query'] ?? '';
$response = ['results' => []];

if (!empty($query)) {
    $conn = new Database();
    $db = $conn->getConnection();
    $stmt = $db->prepare("SELECT id, username FROM users WHERE username LIKE :query LIMIT 5");
    $likeQuery = '%' . $query . '%';
    $stmt->bindParam(':query', $likeQuery);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['results'] = $results;
}

header('Content-Type: application/json');
echo json_encode($response);
