<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - UTPbook</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <!-- Menú de Navegación Superior -->
    <header class="top-nav">
        <div class="logo">UTPbook</div>
        <input type="text" placeholder="Buscar en UTPbook">
        
        <!-- Menú de usuario con avatar y nombre -->
        <div class="user-menu">
            <!-- Avatar predeterminado -->
            <img src="images/Perfil.jpg" alt="Avatar" class="avatar" id="avatarIcon">
            <span class="user-name">Nombre Usuario</span>

            <!-- Menú desplegable al hacer clic en el avatar -->
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profile.php">Ver perfil</a>
                <a href="../backend/controllers/logout-controller.php">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <!-- Estructura Principal -->
    <div class="main-container">
        <!-- Barra Lateral Izquierda -->
        <aside class="sidebar-left">
            <ul>
                <li>Amigos</li>
                <li>Grupos</li>
                <li>Guardado</li>
                <li>Recuerdos</li>
                <li>Configuración</li>
            </ul>
        </aside>

        <!-- Sección Principal -->
        <main class="main-content">
            <!-- Sección para crear una publicación -->
            <div class="create-post">
                <textarea placeholder="¿Qué estás pensando?"></textarea>
                <button>Publicar</button>
            </div>

            <!-- Lista de publicaciones -->
            <div class="post">
                <h3>Nombre del Amigo</h3>
                <p>Este es un ejemplo de publicación en la red social.</p>
            </div>
            <!-- Más publicaciones de ejemplo -->
        </main>

        <!-- Barra Lateral Derecha -->
        <aside class="sidebar-right">
            <h4>Contactos</h4>
            <ul>
                <li>Amigo 1</li>
                <li>Amigo 2</li>
                <li>Amigo 3</li>
            </ul>
        </aside>
    </div>

    <!-- JavaScript para mostrar/ocultar el menú desplegable -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const avatarIcon = document.getElementById('avatarIcon');
            const dropdownMenu = document.getElementById('dropdownMenu');

            avatarIcon.addEventListener('click', function() {
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function(event) {
                if (!avatarIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
