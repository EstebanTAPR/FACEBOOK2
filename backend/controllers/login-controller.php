<?php
include_once '../config/database.php';

include '../functions/user-functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si las credenciales son correctas
    if ($user = loginUser($email, $password)) {
        // Guardar información del usuario en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Redirigir a la página principal
        header("Location: ../../frontend/home.php");
        exit;
    } else {
        echo "Correo o contraseña incorrectos. Inténtalo de nuevo.";
    }
}
?>
