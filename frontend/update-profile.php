<?php
include_once '../backend/config/database.php';
include_once '../backend/controllers/profile-controller.php';

session_start();
$userId = $_SESSION['user_id'];

// Inicializar las rutas de las imágenes en null por si no se cambian
$profilePicturePath = null;
$coverPicturePath = null;

// Verificar y procesar la foto de perfil
if (!empty($_FILES['profile_picture']['name'])) {
    $profilePicturePath = 'images/' . basename($_FILES['profile_picture']['name']);
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profilePicturePath);
}

// Verificar y procesar la foto de portada
if (!empty($_FILES['cover_picture']['name'])) {
    $coverPicturePath = 'images/' . basename($_FILES['cover_picture']['name']);
    move_uploaded_file($_FILES['cover_picture']['tmp_name'], $coverPicturePath);
}

// Guardar la biografía y detalles adicionales
$bio = $_POST['bio'];
$education = $_POST['education'];
$city = $_POST['city'];
$hometown = $_POST['hometown'];

// Actualizar perfil usando la función en el controlador
updateProfile($userId, [
    'bio' => $bio,
    'education' => $education,
    'city' => $city,
    'hometown' => $hometown,
    'profile_picture' => $profilePicturePath,
    'cover_picture' => $coverPicturePath
]);

// Redirigir al perfil del usuario después de guardar los cambios
header("Location: profile.php");
exit;
