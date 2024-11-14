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
            <img src="images/Perfil.jpg" alt="Avatar" class="avatar" id="avatarIcon">
            <span class="user-name">Nombre Usuario</span>
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
                <form action="create-post.php" method="POST" enctype="multipart/form-data">
                    <textarea name="post_content" placeholder="¿Qué estás pensando?"></textarea>
                    <input type="file" name="post_image" accept="image/*">
                    <button type="submit">Publicar</button>
                </form>
            </div>

            <!-- Lista de publicaciones -->
            <?php
            // Incluir la conexión a la base de datos
            include('../backend/config/database.php');
            $conn = new Database();
            $db = $conn->getConnection();

            // Consultar todas las publicaciones de todos los usuarios
            $stmt = $db->prepare("SELECT users.username, posts.content, posts.image, posts.created_at 
                                  FROM posts 
                                  INNER JOIN users ON posts.user_id = users.id 
                                  ORDER BY posts.created_at DESC");
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($posts as $post) {
                echo '<div class="post">';
                echo '<h3>' . htmlspecialchars($post['username']) . '</h3>';
                echo '<p>' . htmlspecialchars($post['content']) . '</p>';
                if (!empty($post['image'])) {
                    echo '<img src="' . htmlspecialchars($post['image']) . '" alt="Imagen de publicación" class="post-image">';
                }
                echo '<p class="post-date">' . htmlspecialchars($post['created_at']) . '</p>';
                echo '</div>';
            }
            ?>
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

    <!-- JavaScript para el menú desplegable -->
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
