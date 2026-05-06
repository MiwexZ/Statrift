<?php
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';
require_once __DIR__ . '/../MODELOS/modelo_perfil.php';

session_start();
if (!isset($_SESSION['nick'])) {
    header("Location: /statrift/VISTAS/login.php");
    exit;
}

$modelo = new modelo_perfil();
$user = $modelo->get_user_data($_SESSION['nick']);
$equipos = $modelo->get_equipos();
$posts = $modelo->get_user_posts($user['id']);

$inicial = strtoupper(substr($user['nick'], 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - StatRift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=1.3">
</head>
<body>
    <?php generar_header(); ?>

    <div class="container py-5 mt-4">
        <div class="row" data-aos="fade-up">
            <!-- COLUMNA IZQUIERDA: DATOS Y FORMULARIO -->
            <div class="col-lg-4 mb-4">
                <div class="bg-glass p-4 rounded-4 text-center mb-4">
                    <div class="post-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; border-width: 3px;">
                        <?= $inicial ?>
                    </div>
                    <h3 class="text-gold fw-bold mb-1"><?= htmlspecialchars($user['nick']) ?></h3>
                    <p class="text-muted mb-0"><?= htmlspecialchars($user['correo']) ?></p>
                    <span class="badge bg-secondary mt-2"><?= htmlspecialchars($user['tipo']) ?></span>
                </div>

                <div class="bg-glass p-4 rounded-4">
                    <h5 class="text-gold mb-3"><i class="fa-solid fa-gear me-2"></i> Ajustes de Perfil</h5>
                    <form id="formPerfil">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Nombre y Apellidos</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" value="<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']) ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light">Equipo Favorito</label>
                            <select id="id_equipo" class="form-select bg-dark text-light border-secondary">
                                <option value="0">Ninguno</option>
                                <?php while($eq = $equipos->fetch_assoc()): ?>
                                    <option value="<?= $eq['id'] ?>" <?= ($eq['id'] == $user['id_equipo_favorito']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($eq['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <?php if($user['equipo_favorito_nombre']): ?>
                                <small class="text-gold mt-1 d-block"><i class="fa-solid fa-shield-halved me-1"></i> Fanático de: <?= htmlspecialchars($user['equipo_favorito_nombre']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-light">Cambiar Contraseña</label>
                            <input type="password" id="pass_nueva" class="form-control bg-dark text-light border-secondary" placeholder="Dejar en blanco para no cambiar">
                        </div>
                        <button type="submit" class="btn btn-gold w-100"><i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios</button>
                    </form>
                </div>
            </div>

            <!-- COLUMNA DERECHA: ACTIVIDAD Y POSTS -->
            <div class="col-lg-8">
                <div class="bg-glass p-4 rounded-4 h-100">
                    <h4 class="text-gold mb-4"><i class="fa-solid fa-clock-rotate-left me-2"></i> Tu Actividad en la Comunidad</h4>
                    
                    <?php if ($posts->num_rows === 0): ?>
                        <div class="text-center text-muted py-5">
                            <i class="fa-solid fa-wind fs-1 mb-3"></i>
                            <p>Aún no has publicado nada.</p>
                            <a href="/statrift/VISTAS/comunidad.php" class="btn btn-outline-gold mt-2">Ir a la Comunidad</a>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush bg-transparent">
                            <?php while($p = $posts->fetch_assoc()): ?>
                            <div class="list-group-item bg-transparent text-light border-secondary px-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-gold"><?= htmlspecialchars($p['titulo']) ?></h6>
                                    <small class="text-muted"><i class="fa-regular fa-calendar-alt me-1"></i> <?= $p['fecha'] ?> a las <?= substr($p['hora'],0,5) ?></small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger btn-delete-mypost" data-id="<?= $p['id'] ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true });

        // Actualizar Perfil
        document.getElementById('formPerfil').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Guardando...';

            const formData = new FormData();
            formData.append('accion', 'update_profile');
            formData.append('id_equipo', document.getElementById('id_equipo').value);
            formData.append('pass_nueva', document.getElementById('pass_nueva').value);

            fetch('/statrift/CONTROLADORES/api_perfil.php', { method: 'POST', body: formData })
            .then(res => res.json()).then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert(data.error);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios';
                }
            });
        });

        // Borrar mi post
        document.querySelectorAll('.btn-delete-mypost').forEach(btn => {
            btn.addEventListener('click', function() {
                if(!confirm('¿Seguro que quieres borrar tu publicación? Esta acción es irreversible.')) return;
                
                const id = this.getAttribute('data-id');
                const formData = new FormData();
                formData.append('accion', 'delete_my_post');
                formData.append('id_post', id);

                fetch('/statrift/CONTROLADORES/api_perfil.php', { method: 'POST', body: formData })
                .then(res => res.json()).then(data => {
                    if(data.success) location.reload();
                    else alert(data.error);
                });
            });
        });
    </script>
</body>
</html>
