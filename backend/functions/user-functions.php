<?php
include_once '../config/database.php';




function emailExists($email) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT id FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}


function registerUser($first_name, $last_name, $email, $password, $gender, $birth_date) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "INSERT INTO users (username, email, password, profile_picture, bio, created_at, gender, birth_date) 
              VALUES (:username, :email, :password, NULL, '', NOW(), :gender, :birth_date)";
    $stmt = $db->prepare($query);
    
    // Crear un nombre de usuario concatenando nombre y apellido
    $username = $first_name . " " . $last_name;
    
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':birth_date', $birth_date);

    return $stmt->execute();
}

// Función para iniciar sesión
function loginUser($email, $password) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar que el usuario existe y que la contraseña coincide
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}


?>
