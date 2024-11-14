<?php
// Iniciar la sesión
session_start();

// Incluir la clase de conexión a la base de datos
include('../backend/config/database.php');

// Crear una instancia de la clase Database y obtener la conexión
$conn = new Database();
$db = $conn->getConnection();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Redirigir al usuario al login si no está autenticado
    header("Location: login.php");
    exit;
}

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Consulta para obtener la información del usuario
$query = $db->prepare("SELECT username, profile_picture, cover_picture, bio, education, city, hometown FROM users WHERE id = :user_id");
$query->bindParam(':user_id', $user_id);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

// Variables para mostrar información del usuario
$nombre = $user['username'];
$amigos = 0;  // Inicialmente 0 porque todavía no estamos contando amigos
$bio = $user['bio'] ?: '---';
$profile_picture = $user['profile_picture'] ?: 'images/default-avatar.png';
$cover_picture = $user['cover_picture'] ?: 'images/default-cover.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - UTPbook</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <!-- Cabecera del Perfil -->
    <header class="profile-header">
        <div class="cover-container">
            <img src="<?php echo htmlspecialchars($cover_picture); ?>" alt="Foto de Portada" class="cover-photo">
            <div class="profile-photo-container">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Foto de Perfil" class="profile-avatar">
            </div>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($nombre); ?></h1>
            <p><?php echo $amigos; ?> amigos</p>
            <div class="profile-actions">
                <button>Agregar a Historia</button>
                <a href="edit-profile.php">
                    <button>Editar Perfil</button>
                </a>
            </div>
        </div>
    </header>

    <!-- Menú de Navegación del Perfil -->
    <nav class="profile-nav">
        <ul>
            <li><a href="#">Publicaciones</a></li>
            <li><a href="#">Información</a></li>
            <li><a href="#">Amigos</a></li>
            <li><a href="#">Fotos</a></li>
            <li><a href="#">Videos</a></li>
            <li><a href="#">Más</a></li>
        </ul>
    </nav>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Sección de Información Personal -->
        <aside class="profile-sidebar">
            <h3>Detalles</h3>
            <p>Bio: <?php echo htmlspecialchars($bio); ?></p>
            <p>Estudió en: <?php echo htmlspecialchars($user['education'] ?? '---'); ?></p>
            <p>Vive en: <?php echo htmlspecialchars($user['city'] ?? '---'); ?></p>
            <p>De: <?php echo htmlspecialchars($user['hometown'] ?? '---'); ?></p>
        </aside>

        <!-- Sección de Publicaciones -->
        <section class="profile-posts">
            <div class="create-post">
                <form action="create-post.php" method="POST" enctype="multipart/form-data">
                    <textarea name="post_content" placeholder="¿Qué estás pensando?"></textarea>
                    <input type="file" name="post_image" accept="image/*">
                    <button type="submit">Publicar</button>
                </form>
            </div>

            <!-- Publicaciones del usuario -->
            <?php
            // Obtener las publicaciones del usuario
            $stmt = $db->prepare("SELECT content, image, created_at FROM posts WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($posts as $post) {
                echo '<div class="post">';
                echo '<h4>' . htmlspecialchars($nombre) . '</h4>';
                echo '<p>' . htmlspecialchars($post['content']) . '</p>';
                if (!empty($post['image'])) {
                    echo '<img src="' . htmlspecialchars($post['image']) . '" alt="Imagen de publicación">';
                }
                echo '<p class="post-date">' . htmlspecialchars($post['created_at']) . '</p>';
                echo '</div>';
            }
            ?>
        </section>
    </div>
</body>
</html>
