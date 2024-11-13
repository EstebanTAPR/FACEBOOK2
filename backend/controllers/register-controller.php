<?php
include_once '../config/database.php';

include '../functions/user-functions.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $birth_day = $_POST['day'];
    $birth_month = $_POST['month'];
    $birth_year = $_POST['year'];
    $birth_date = "$birth_year-$birth_month-$birth_day";

    // Validar si el correo electrónico ya existe
    if (emailExists($email)) {
        echo "El correo electrónico ya está registrado. Intenta con otro.";
        exit;
    }

    // Encriptar la contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Crear el usuario en la base de datos
    if (registerUser($first_name, $last_name, $email, $hashed_password, $gender, $birth_date)) {
        // Redirigir a login en caso de registro exitoso
        header("Location: ../../frontend/login.php");
        exit;
    } else {
        echo "Hubo un error al registrar el usuario. Inténtalo de nuevo.";
    }
}
?>
