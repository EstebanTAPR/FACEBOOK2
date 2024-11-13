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
        <!-- Sección de Foto de Perfil -->
        <section class="profile-section">
            <h2>Foto del Perfil</h2>
            <img src="images/Perfil.jpg" alt="Foto de Perfil" class="profile-avatar">
            <button>Cambiar foto de perfil</button>
        </section>

        <!-- Sección de Foto de Portada -->
        <section class="cover-section">
            <h2>Foto de Portada</h2>
            <img src="images/cover-photo.jpg" alt="Foto de Portada" class="cover-photo">
            <button>Cambiar foto de portada</button>
        </section>

        <!-- Sección de Avatar -->
        <section class="avatar-section">
            <h2>Avatar</h2>
            <div class="avatar-options">
                <!-- Avatares de ejemplo -->
                <img src="images/avatar1.jpg" alt="Avatar 1">
                <img src="images/avatar2.jpg" alt="Avatar 2">
                <img src="images/avatar3.jpg" alt="Avatar 3">
                <!-- Botón para crear o cambiar avatar -->
            </div>
            <button>Crear avatar</button>
        </section>

        <!-- Sección de Presentación -->
        <section class="bio-section">
            <h2>Presentación</h2>
            <textarea placeholder="Descríbete..."></textarea>
            <button>Guardar presentación</button>
        </section>

        <!-- Sección de Personalizar Detalles -->
        <section class="details-section">
            <h2>Personaliza tus detalles</h2>
            <div class="details">
                <p>Estudió en <input type="text" value="Nuestra Señora de Montserrat"></p>
                <p>Vive en <input type="text" value="Lima"></p>
                <p>De <input type="text" value="Chiclayo"></p>
            </div>
            <button>Guardar detalles</button>
        </section>
    </div>
</body>
</html>
