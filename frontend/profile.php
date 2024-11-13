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
$query = $db->prepare("SELECT username, profile_picture, bio, created_at FROM users WHERE id = :user_id");
$query->bindParam(':user_id', $user_id);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

// Variables para mostrar información del usuario
$nombre = $user['username'];
$amigos = 0;  // Inicialmente 0 porque todavía no estamos contando amigos
$bio = $user['bio'] ?: '---';
$profile_picture = $user['profile_picture'] ?: 'images/default-avatar.png';
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
        <div class="profile-info">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Foto de Perfil" class="profile-avatar">
            <div class="profile-details">
                <h1><?php echo htmlspecialchars($nombre); ?></h1>
                <p><?php echo $amigos; ?> amigos</p>
            </div>
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
            <button>Editar detalles</button>
        </aside>

        <!-- Sección de Publicaciones -->
        <section class="profile-posts">
            <div class="create-post">
                <textarea placeholder="¿Qué estás pensando?"></textarea>
                <button>Publicar</button>
            </div>

            <!-- Publicaciones de ejemplo -->
            <div class="post">
                <h4><?php echo htmlspecialchars($nombre); ?></h4>
                <p>Este es un ejemplo de publicación en el perfil.</p>
            </div>
            <!-- Puedes añadir más publicaciones aquí -->
        </section>
    </div>
</body>
</html>
