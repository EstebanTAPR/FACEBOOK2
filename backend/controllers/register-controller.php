<?php
include_once '../config/database.php';
include '../functions/user-functions.php';

// Función para registrar eventos de seguridad en un archivo de log
function logSecurityEvent($message) {
    file_put_contents('../logs/security.log', date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL, FILE_APPEND);
}

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar entradas para evitar XSS
    $first_name = htmlspecialchars(trim($_POST['first_name']), ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars(trim($_POST['last_name']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $gender = htmlspecialchars(trim($_POST['gender']), ENT_QUOTES, 'UTF-8');
    $birth_day = (int)$_POST['day'];
    $birth_month = (int)$_POST['month'];
    $birth_year = (int)$_POST['year'];
    $birth_date = "$birth_year-$birth_month-$birth_day";

    // Validar si el correo electrónico ya existe
    if (emailExists($email)) {
        echo "El correo electrónico ya está registrado. Intenta con otro.";
        logSecurityEvent("Intento de registro con correo existente: $email");
        exit;
    }

    // Encriptar la contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Intentar registrar el usuario
    if (registerUser($first_name, $last_name, $email, $hashed_password, $gender, $birth_date)) {
        // Registrar evento de creación de usuario
        logSecurityEvent("Usuario registrado exitosamente: $email");

        // Redirigir a login en caso de registro exitoso
        header("Location: ../../frontend/login.php");
        exit;
    } else {
        echo "Hubo un error al registrar el usuario. Inténtalo de nuevo.";
        logSecurityEvent("Error al registrar usuario: $email");
    }
} else {
    echo "Método de solicitud no permitido.";
    logSecurityEvent("Intento de acceso no autorizado a register-controller.php");
}
?>
