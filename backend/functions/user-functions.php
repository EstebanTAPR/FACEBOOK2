<?php
include_once '../config/database.php';
include_once __DIR__ . '/../config/database.php';




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

function updateUserDetails($conn, $user_id, $data) {
    // Actualizar detalles del perfil
    $stmt = $conn->prepare("UPDATE users SET bio = :bio, education = :education, city = :city, hometown = :hometown WHERE id = :user_id");
    $stmt->bindParam(':bio', $data['bio']);
    $stmt->bindParam(':education', $data['education']);
    $stmt->bindParam(':city', $data['city']);
    $stmt->bindParam(':hometown', $data['hometown']);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        // Si se envían archivos de imagen, llámalos aquí para manejar la carga
        if ($data['profile_picture']) {
            uploadImage($conn, $user_id, $data['profile_picture'], 'profile_picture');
        }
        if ($data['cover_picture']) {
            uploadImage($conn, $user_id, $data['cover_picture'], 'cover_picture');
        }
        return true;
    } else {
        return false;
    }
}

function uploadImage($conn, $user_id, $file, $type) {
    $target_dir = "../frontend/images/";
    $target_file = $target_dir . basename($file["name"]);
    move_uploaded_file($file["tmp_name"], $target_file);

    $column = $type === 'profile_picture' ? 'profile_picture' : 'cover_picture';
    $stmt = $conn->prepare("UPDATE users SET $column = :image WHERE id = :user_id");
    $stmt->bindParam(':image', $target_file);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}


?>
