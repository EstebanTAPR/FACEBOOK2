<?php
include_once '../config/database.php';
include '../functions/user-functions.php';

session_start();

// Función para registrar eventos de seguridad en un archivo de log
function logSecurityEvent($message) {
    file_put_contents('../logs/security.log', date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL, FILE_APPEND);
}

// Configuración de intentos fallidos y bloqueo
define('MAX_FAILED_ATTEMPTS', 3);
define('LOCKOUT_TIME', 12 * 60 * 60); // 12 horas en segundos

// Función para verificar si un usuario está bloqueado
function isUserLockedOut($email) {
    $lockoutFile = '../logs/lockout_' . md5($email) . '.json';
    
    if (file_exists($lockoutFile)) {
        $data = json_decode(file_get_contents($lockoutFile), true);
        if (time() < $data['unlock_time']) {
            return true;
        } else {
            // El tiempo de bloqueo ha pasado, eliminar el archivo de bloqueo
            unlink($lockoutFile);
        }
    }
    return false;
}

// Función para registrar un intento fallido y bloquear al usuario si es necesario
function recordFailedAttempt($email) {
    $lockoutFile = '../logs/lockout_' . md5($email) . '.json';
    $data = ['failed_attempts' => 1, 'unlock_time' => 0];

    if (file_exists($lockoutFile)) {
        $data = json_decode(file_get_contents($lockoutFile), true);
        $data['failed_attempts']++;
    }

    if ($data['failed_attempts'] >= MAX_FAILED_ATTEMPTS) {
        $data['unlock_time'] = time() + LOCKOUT_TIME;
        logSecurityEvent("Usuario bloqueado: $email después de " . MAX_FAILED_ATTEMPTS . " intentos fallidos.");
        echo "Demasiados intentos fallidos. Por favor, intenta de nuevo en 12 horas.";
    } else {
        echo "Correo o contraseña incorrectos. Intento " . $data['failed_attempts'] . " de " . MAX_FAILED_ATTEMPTS . ".";
    }

    file_put_contents($lockoutFile, json_encode($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el usuario está bloqueado
    if (isUserLockedOut($email)) {
        echo "Demasiados intentos fallidos. Por favor, intenta de nuevo en 12 horas.";
        logSecurityEvent("Intento de inicio de sesión durante el periodo de bloqueo para el usuario: $email");
        exit;
    }

    // Intentar iniciar sesión
    if ($user = loginUser($email, $password)) {
        // Inicio de sesión exitoso, limpiar intentos fallidos
        $lockoutFile = '../logs/lockout_' . md5($email) . '.json';
        if (file_exists($lockoutFile)) {
            unlink($lockoutFile); // Eliminar archivo de bloqueo en caso de éxito
        }
        
        // Guardar información del usuario en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Redirigir a la página principal
        header("Location: ../../frontend/home.php");
        exit;
    } else {
        // Credenciales incorrectas, registrar intento fallido
        recordFailedAttempt($email);
        logSecurityEvent("Intento de inicio de sesión fallido para el usuario: $email");
    }
}
?>
