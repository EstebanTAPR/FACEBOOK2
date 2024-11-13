<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro en UTPbook</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="container">
      
        <div class="right-section">
            <h2>Crea una cuenta nueva</h2>
            <p>Es rápido y fácil.</p>
            <form action="../backend/controllers/register-controller.php" method="POST" class="register-form">
                <div class="name-fields">
                    <input type="text" name="first_name" placeholder="Nombre" required>
                    <input type="text" name="last_name" placeholder="Apellido" required>
                </div>
                
                <label>Fecha de nacimiento</label>
                <div class="date-fields">
                    <select name="day" required>
                        <option value="">Día</option>
                        <?php for ($i = 1; $i <= 31; $i++) echo "<option value='$i'>$i</option>"; ?>
                    </select>
                    <select name="month" required>
                        <option value="">Mes</option>
                        <option value="1">Ene</option>
                        <option value="2">Feb</option>
                        <option value="3">Mar</option>
                        <option value="4">Abr</option>
                        <option value="5">May</option>
                        <option value="6">Jun</option>
                        <option value="7">Jul</option>
                        <option value="8">Ago</option>
                        <option value="9">Sep</option>
                        <option value="10">Oct</option>
                        <option value="11">Nov</option>
                        <option value="12">Dic</option>
                    </select>
                    <select name="year" required>
                        <option value="">Año</option>
                        <?php for ($i = date("Y"); $i >= 1900; $i--) echo "<option value='$i'>$i</option>"; ?>
                    </select>
                </div>
                
                <label>Género</label>
                <div class="gender-fields">
                    <label><input type="radio" name="gender" value="Mujer" required> Mujer</label>
                    <label><input type="radio" name="gender" value="Hombre" required> Hombre</label>
                    <label><input type="radio" name="gender" value="Personalizado" required> Personalizado</label>
                </div>

                <input type="email" name="email" placeholder="Número de celular o correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña nueva" required>

                <button type="submit" class="register-button">Registrarte</button>

                <p class="login-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
            </form>
        </div>
    </div>
</body>
</html>
