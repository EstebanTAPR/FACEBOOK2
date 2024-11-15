<?php
session_start();
include('../backend/config/database.php');

// Verifica que el usuario haya iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new Database();
$db = $conn->getConnection();

// Obtiene el nombre de usuario
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT username FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? htmlspecialchars($user['username']) : 'Nombre Usuario';
?>
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
        <div class="search-container">
            <input type="text" id="search-box" placeholder="Buscar en UTPbook" oninput="searchUser()">
            <div id="search-results" class="search-results"></div>
        </div>
        
        <!-- Menú de usuario con avatar y nombre -->
        <div class="user-menu">
            <img src="images/Perfil.jpg" alt="Avatar" class="avatar" id="avatarIcon">
            <span class="user-name"><?php echo $username; ?></span>
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
                <form id="create-post-form" enctype="multipart/form-data">
                    <textarea name="post_content" placeholder="¿Qué estás pensando?" required></textarea>
                    <div class="custom-file-container">
                        <label for="post-image" class="custom-file-label">Seleccionar archivo</label>
                        <input id="post-image" type="file" name="post_image" accept="image/*">
                        <button type="submit">Publicar</button>
                    </div>
                </form>
            </div>

            <!-- Lista de publicaciones -->
            <div id="posts-container">
            <?php
            // Consultar todas las publicaciones de todos los usuarios
            $stmt = $db->prepare("SELECT posts.id, users.username, posts.content, posts.image, posts.created_at 
                                  FROM posts 
                                  INNER JOIN users ON posts.user_id = users.id 
                                  ORDER BY posts.created_at DESC");
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($posts as $post) {
                echo '<div class="post" id="post-' . $post['id'] . '">';
                echo '<h3>' . htmlspecialchars($post['username']) . '</h3>';
                echo '<p>' . htmlspecialchars($post['content']) . '</p>';
                if (!empty($post['image'])) {
                    echo '<img src="' . htmlspecialchars($post['image']) . '" alt="Imagen de publicación" class="post-image">';
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
                echo '<button onclick="addReaction(' . $post['id'] . ', \'like\')">👍 Like (' . $reactionCounts['like'] . ')</button>';
                echo '<button onclick="addReaction(' . $post['id'] . ', \'love\')">❤️ Love (' . $reactionCounts['love'] . ')</button>';
                echo '<button onclick="addReaction(' . $post['id'] . ', \'haha\')">😂 Haha (' . $reactionCounts['haha'] . ')</button>';
                echo '</div>';

                // Sección de Comentarios
                echo '<div class="comments" id="comments-section-' . $post['id'] . '">';
                echo '<form onsubmit="addComment(event, ' . $post['id'] . ')">';
                echo '<textarea name="comment_content" placeholder="Escribe un comentario..." required></textarea>';
                echo '<button type="submit">Comentar</button>';
                echo '</form>';

                // Mostrar comentarios
                $commentStmt = $db->prepare("SELECT comments.content, comments.created_at, users.username 
                                             FROM comments 
                                             JOIN users ON comments.user_id = users.id 
                                             WHERE post_id = :post_id 
                                             ORDER BY comments.created_at DESC");
                $commentStmt->bindParam(':post_id', $post['id']);
                $commentStmt->execute();
                $comments = $commentStmt->fetchAll();

                foreach ($comments as $comment) {
                    echo '<div class="comment">';
                    echo '<p><strong>' . htmlspecialchars($comment['username']) . ':</strong> ' . htmlspecialchars($comment['content']) . '</p>';
                    echo '<p class="comment-date">' . htmlspecialchars($comment['created_at']) . '</p>';
                    echo '</div>';
                }

                echo '</div>'; // Cierra comentarios
                echo '</div>'; // Cierra publicación
            }
            ?>
            </div>
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

    <!-- JavaScript para el menú desplegable y las interacciones -->
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

        // Función para realizar búsqueda en tiempo real
        function searchUser() {
            const query = document.getElementById('search-box').value;
            if (query.length > 1) {
                fetch(`../backend/controllers/search-users.php?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        const resultsContainer = document.getElementById('search-results');
                        resultsContainer.innerHTML = '';
                        data.results.forEach(user => {
                            const resultItem = document.createElement('div');
                            resultItem.classList.add('result-item');
                            resultItem.innerHTML = `<a href="view-profile.php?user_id=${user.id}">${user.username}</a>`;
                            resultsContainer.appendChild(resultItem);
                        });
                    });
            } else {
                document.getElementById('search-results').innerHTML = '';
            }
        }

        // Función para añadir una reacción
        function addReaction(postId, reactionType) {
            fetch('../backend/controllers/add-reaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'post_id=' + postId + '&reaction_type=' + reactionType
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const postElement = document.getElementById('post-' + postId);
                    postElement.querySelector('.reactions').innerHTML = `
                        <button onclick="addReaction(${postId}, 'like')">👍 Like (${data.reactions.like})</button>
                        <button onclick="addReaction(${postId}, 'love')">❤️ Love (${data.reactions.love})</button>
                        <button onclick="addReaction(${postId}, 'haha')">😂 Haha (${data.reactions.haha})</button>
                    `;
                } else {
                    console.error("Error en la reacción:", data.error);
                }
            })
            .catch(error => console.error("Error de red:", error));
        }

        // Función para añadir un comentario sin recargar
        function addComment(event, postId) {
            event.preventDefault();
            const form = event.target;
            const commentContent = form.querySelector('textarea[name="comment_content"]').value;

            fetch('../backend/controllers/add-comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'post_id=' + postId + '&comment_content=' + encodeURIComponent(commentContent)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const commentsSection = document.getElementById('comments-section-' + postId);
                    const newComment = document.createElement('div');
                    newComment.classList.add('comment');
                    newComment.innerHTML = `
                        <p><strong>${data.comment.username}:</strong> ${data.comment.content}</p>
                        <p class="comment-date">${data.comment.created_at}</p>
                    `;
                    commentsSection.appendChild(newComment);
                } else {
                    console.error("Error en el comentario:", data.error);
                }
            })
            .catch(error => console.error("Error de red:", error));

            form.reset();
        }

        // Función para crear una publicación sin recargar
        document.getElementById('create-post-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('../backend/controllers/create-post.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const post = data.post;
                    const postContainer = document.createElement('div');
                    postContainer.classList.add('post');
                    postContainer.id = 'post-' + post.id;
                    postContainer.innerHTML = `
                        <h3>${post.username}</h3>
                        <p>${post.content}</p>
                        ${post.image ? `<img src="${post.image}" alt="Imagen de publicación" class="post-image">` : ''}
                        <p class="post-date">${post.created_at}</p>
                        <div class="reactions">
                            <button onclick="addReaction(${post.id}, 'like')">👍 Like (0)</button>
                            <button onclick="addReaction(${post.id}, 'love')">❤️ Love (0)</button>
                            <button onclick="addReaction(${post.id}, 'haha')">😂 Haha (0)</button>
                        </div>
                        <div class="comments" id="comments-section-${post.id}">
                            <form onsubmit="addComment(event, ${post.id})">
                                <textarea name="comment_content" placeholder="Escribe un comentario..." required></textarea>
                                <button type="submit">Comentar</button>
                            </form>
                        </div>
                    `;
                    document.getElementById('posts-container').prepend(postContainer);
                    document.getElementById('create-post-form').reset();
                } else {
                    console.error('Error al crear la publicación:', data.error);
                }
            })
            .catch(error => console.error('Error de red:', error));
        });
    </script>
</body>
</html>
