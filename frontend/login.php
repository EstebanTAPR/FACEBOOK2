<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión en UTPbook</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        
        <div class="left-section">
            <h1>UTPbook</h1>
            <p>UTPbook te ayuda a comunicarte y compartir con las personas que forman parte de tu vida.</p>
        </div>
        
       
        <div class="right-section">
            <form action="../backend/controllers/login-controller.php" method="POST" class="login-form">
                <input type="text" name="email" placeholder="Correo electrónico o número de teléfono" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" class="login-button">Iniciar sesión</button>
                <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                <hr>
                <button type="button" class="register-button" onclick="window.location.href='register.php'">Crear cuenta nueva</button>
            </form>
        </div>
    </div>
</body>
</html>
