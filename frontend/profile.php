<?php
// Iniciar la sesión
session_start();

// Incluir la clase de conexión a la base de datos
include('../backend/config/database.php');

// Crear una instancia de la clase Database y obtener la conexión
$conn = new Database();
$db = $conn->getConnection();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Redirigir al usuario al login si no está autenticado
    header("Location: login.php");
    exit;
}

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Consulta para obtener la información del usuario
$query = $db->prepare("SELECT username, profile_picture, cover_picture, bio, education, city, hometown FROM users WHERE id = :user_id");
$query->bindParam(':user_id', $user_id);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

// Variables para mostrar información del usuario
$nombre = $user['username'];
$amigos = 0;  // Inicialmente 0 porque todavía no estamos contando amigos
$bio = $user['bio'] ?: '---';
$profile_picture = $user['profile_picture'] ?: 'images/default-avatar.png';
$cover_picture = $user['cover_picture'] ?: 'images/default-cover.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - UTPbook</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
    <!-- Cabecera del Perfil -->
     <header class="top-header">
    <div class="header-left">
        <a href="home.php" class="logo">UTPbook</a> <!-- Cambié el logo a texto "UTPbook" -->
        <input type="text" placeholder="Buscar en UTPbook" class="search-input">
    </div>
    <div class="header-center">
    <a href="home.php" class="nav-icon"><i class="fas fa-home"></i></a>
    <a href="#" class="nav-icon"><i class="fas fa-user-friends"></i></a>
    <a href="#" class="nav-icon"><i class="fas fa-store"></i></a>
    <a href="#" class="nav-icon"><i class="fas fa-video"></i></a>
</div>

    <div class="header-right">
        <a href="profile.php" class="nav-icon"><i class="fa fa-user-circle"></i></a>
        <a href="notifications.php" class="nav-icon"><i class="fa fa-bell"></i><span class="badge">20+</span></a>
        <a href="settings.php" class="nav-icon"><i class="fa fa-cog"></i></a>
    </div>
</header>

    <header class="profile-header">

    
        <div class="cover-container">
            <img src="<?php echo htmlspecialchars($cover_picture); ?>" alt="Foto de Portada" class="cover-photo">
            <div class="profile-photo-container">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Foto de Perfil" class="profile-avatar">
            </div>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($nombre); ?></h1>
            <p><?php echo $amigos; ?> amigos</p>
            <div class="profile-actions">
                <button>Agregar a Historia</button>
                <a href="edit-profile.php">
                    <button>Editar Perfil</button>
                </a>
            </div>
        </div>
    </header>

    <!-- Menú de Navegación del Perfil -->
    <nav class="profile-nav">
        <ul>
            <li><a href="#">Publicaciones</a></li>
            <li><a href="#">Información</a></li>
            <li><a href="#">Amigos</a></li>
            <li><a href="#">Fotos</a></li>
            <li><a href="#">Videos</a></li>
            <li><a href="#">Más</a></li>
        </ul>
    </nav>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Sección de Información Personal -->
        <aside class="profile-sidebar">
            <h3>Detalles</h3>
            <p>Bio: <?php echo htmlspecialchars($bio); ?></p>
            <p>Estudió en: <?php echo htmlspecialchars($user['education'] ?? '---'); ?></p>
            <p>Vive en: <?php echo htmlspecialchars($user['city'] ?? '---'); ?></p>
            <p>De: <?php echo htmlspecialchars($user['hometown'] ?? '---'); ?></p>
        </aside>

        <!-- Sección de Publicaciones -->
        <section class="profile-posts">
            <div class="create-post">
                <form action="create-post.php" method="POST" enctype="multipart/form-data">
                    <textarea name="post_content" placeholder="¿Qué estás pensando?"></textarea>
                    <input type="file" name="post_image" accept="image/*" id="postImageInput">
                    <img id="imagePreview" src="#" alt="Previsualización de Imagen" style="display: none; max-width: 100%; margin-top: 10px;">
                    <button type="submit">Publicar</button>
                </form>
            </div>

            <script>
                document.getElementById('postImageInput').addEventListener('change', function(event) {
                    const imagePreview = document.getElementById('imagePreview');
                    const file = event.target.files[0];
                    
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.style.display = 'none';
                    }
                });

                function addReaction(postId, reactionType) {
                    fetch('../backend/controllers/add-reaction.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `post_id=${postId}&reaction_type=${reactionType}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`#like-count-${postId}`).innerText = `(${data.reactions.like})`;
                            document.querySelector(`#love-count-${postId}`).innerText = `(${data.reactions.love})`;
                            document.querySelector(`#haha-count-${postId}`).innerText = `(${data.reactions.haha})`;
                        } else {
                            console.error("Error al agregar la reacción.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
                }
            </script>

            <!-- Publicaciones del usuario -->
            <?php
            // Obtener las publicaciones del usuario
            $stmt = $db->prepare("SELECT id, content, image, created_at FROM posts WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($posts as $post) {
                echo '<div class="post">';
                echo '<h4>' . htmlspecialchars($nombre) . '</h4>';
                echo '<p>' . htmlspecialchars($post['content']) . '</p>';
                if (!empty($post['image'])) {
                    echo '<img src="' . htmlspecialchars($post['image']) . '" alt="Imagen de publicación">';
                }
                echo '<p class="post-date">' . htmlspecialchars($post['created_at']) . '</p>';
                
                // Obtener conteo de reacciones
                $reactionStmt = $db->prepare("SELECT reaction_type, COUNT(*) as count FROM reactions WHERE post_id = :post_id GROUP BY reaction_type");
                $reactionStmt->bindParam(':post_id', $post['id']);
                $reactionStmt->execute();
                $reactions = $reactionStmt->fetchAll(PDO::FETCH_ASSOC);

                $reactionCounts = ['like' => 0, 'love' => 0, 'haha' => 0];
                foreach ($reactions as $reaction) {
                    $reactionCounts[$reaction['reaction_type']] = $reaction['count'];
                }

                // Sección de Reacciones con contadores
                echo '<div class="reactions">';
                echo '<button onclick="addReaction(' . $post['id'] . ', \'like\')">👍 Like <span id="like-count-' . $post['id'] . '">(' . $reactionCounts['like'] . ')</span></button>';
                echo '<button onclick="addReaction(' . $post['id'] . ', \'love\')">❤️ Love <span id="love-count-' . $post['id'] . '">(' . $reactionCounts['love'] . ')</span></button>';
                echo '<button onclick="addReaction(' . $post['id'] . ', \'haha\')">😂 Haha <span id="haha-count-' . $post['id'] . '">(' . $reactionCounts['haha'] . ')</span></button>';
                echo '</div>';
                
                // Sección de Comentarios
                echo '<div class="comments">';
                echo '<form action="add-comment.php" method="POST">';
                echo '<input type="hidden" name="post_id" value="' . $post['id'] . '">';
                echo '<textarea name="comment_content" placeholder="Escribe un comentario..."></textarea>';
                echo '<button type="submit">Comentar</button>';
                echo '</form>';
                
                // Mostrar comentarios
                $commentStmt = $db->prepare("SELECT * FROM comments WHERE post_id = :post_id ORDER BY created_at DESC");
                $commentStmt->bindParam(':post_id', $post['id']);
                $commentStmt->execute();
                $comments = $commentStmt->fetchAll();

                foreach ($comments as $comment) {
                    echo '<div class="comment">';
                    echo '<p><strong>' . htmlspecialchars($nombre) . ':</strong> ' . htmlspecialchars($comment['content']) . '</p>';
                    echo '<p class="comment-date">' . htmlspecialchars($comment['created_at']) . '</p>';
                    echo '</div>';
                }

                echo '</div>'; // Cierra comentarios
                echo '</div>'; // Cierra publicación
            }
            ?>
        </section>
    </div>
</body>
</html>
