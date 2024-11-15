<?php
// Obtener ID del usuario desde el parámetro de la URL
$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo "Usuario no encontrado.";
    exit;
}

include_once '../backend/config/database.php';

$conn = new Database();
$db = $conn->getConnection();
$stmt = $db->prepare("SELECT username, profile_picture, cover_picture, bio, education, city, hometown FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Usuario no encontrado.";
    exit;
}

$nombre = $user['username'];
$bio = $user['bio'] ?: '---';
$profile_picture = $user['profile_picture'] ?: 'images/default-avatar.png';
$cover_picture = $user['cover_picture'] ?: 'images/default-cover.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <header class="profile-header">
        <div class="cover-container">
            <img src="<?php echo htmlspecialchars($cover_picture); ?>" alt="Foto de Portada" class="cover-photo">
            <div class="profile-photo-container">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Foto de Perfil" class="profile-avatar">
            </div>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($nombre); ?></h1>
            <p>Biografía: <?php echo htmlspecialchars($bio); ?></p>
            <p>Ciudad: <?php echo htmlspecialchars($user['city'] ?? '---'); ?></p>
        </div>
    </header>

    <main class="main-content">
        <section class="profile-posts">
            <?php
            $stmt = $db->prepare("SELECT content, image, created_at FROM posts WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($posts as $post) {
                echo '<div class="post">';
                echo '<p>' . htmlspecialchars($post['content']) . '</p>';
                if (!empty($post['image'])) {
                    echo '<img src="' . htmlspecialchars($post['image']) . '" alt="Imagen de publicación" class="post-image">';
                }
                echo '<p class="post-date">' . htmlspecialchars($post['created_at']) . '</p>';
                echo '</div>';
            }
            ?>
        </section>
    </main>
</body>
</html>
