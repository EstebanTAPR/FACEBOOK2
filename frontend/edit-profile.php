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

// Consulta para obtener la información actual del usuario
$query = $db->prepare("SELECT profile_picture, cover_picture, bio, education, city, hometown FROM users WHERE id = :user_id");
$query->bindParam(':user_id', $user_id);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

// Variables para mostrar información del usuario
$profile_picture = $user['profile_picture'] ?? 'images/default-avatar.png';
$cover_picture = $user['cover_picture'] ?? 'images/default-cover.jpg';
$bio = $user['bio'] ?? '';
$education = $user['education'] ?? '';
$city = $user['city'] ?? '';
$hometown = $user['hometown'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - UTPbook</title>
    <link rel="stylesheet" href="css/edit-profile.css">
</head>
<body>
    <div class="edit-profile-container">
        <!-- Formulario de edición de perfil -->
        <form action="update-profile.php" method="POST" enctype="multipart/form-data">
            <!-- Sección de Foto de Perfil -->
            <section class="profile-section">
                <h2>Foto del Perfil</h2>
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Foto de Perfil" class="profile-avatar" id="profilePreview">
                <input type="file" name="profile_picture" accept="image/*" onchange="previewImage(event, 'profilePreview')">
            </section>

            <!-- Sección de Foto de Portada -->
            <section class="cover-section">
                <h2>Foto de Portada</h2>
                <img src="<?php echo htmlspecialchars($cover_picture); ?>" alt="Foto de Portada" class="cover-photo" id="coverPreview">
                <input type="file" name="cover_picture" accept="image/*" onchange="previewImage(event, 'coverPreview')">
            </section>

            <!-- Sección de Presentación (Biografía) -->
            <section class="bio-section">
                <h2>Presentación</h2>
                <textarea name="bio" placeholder="Descríbete..."><?php echo htmlspecialchars($bio); ?></textarea>
            </section>

            <!-- Sección de Personaliza tus Detalles -->
            <section class="details-section">
                <h2>Personaliza tus detalles</h2>
                <div class="details">
                    <label>Estudió en: <input type="text" name="education" value="<?php echo htmlspecialchars($education); ?>"></label>
                    <label>Vive en: <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>"></label>
                    <label>De: <input type="text" name="hometown" value="<?php echo htmlspecialchars($hometown); ?>"></label>
                </div>
            </section>

            <!-- Botón para guardar todos los cambios -->
            <button type="submit">Guardar cambios</button>
        </form>
    </div>

    <!-- Script de previsualización de imágenes -->
    <script>
        function previewImage(event, previewId) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById(previewId);
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
