<?php
ob_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';
require_once __DIR__ . '/../MODELOS/modelo_admin.php';

session_start();
if (!isset($_SESSION['nick']) || $_SESSION['nick'] !== 'admin') {
    header("Location: /index.php");
    exit;
}

$modelo = new modelo_admin();
$stats = $modelo->get_stats();
$publicaciones = $modelo->get_publicaciones();
$usuarios = $modelo->get_usuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - StatRift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=1.3">
    <style>
        .admin-sidebar {
            background: rgba(15, 23, 42, 0.8);
            border-right: 1px solid rgba(197, 160, 89, 0.2);
            min-height: calc(100vh - 76px);
        }
        .nav-pills .nav-link {
            color: var(--text-light);
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .nav-pills .nav-link.active, .nav-pills .nav-link:hover {
            background-color: rgba(197, 160, 89, 0.2);
            color: var(--gold-accent);
        }
        .stat-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8), rgba(197, 160, 89, 0.15));
            border: 1px solid rgba(197, 160, 89, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: var(--gold-accent); }
    </style>
</head>
<body>
    <?php
    // Evitamos llamar a generar_header() aquí porque ya hemos iniciado sesión en este archivo,
    // y generar_header() llama a session_start() de nuevo. Lo llamamos de forma segura:
    if(session_status() === PHP_SESSION_NONE) session_start();
    ?>
    <nav class='navbar navbar-expand-lg sticky-top'>
        <div class='container-fluid px-4'>
            <a class='navbar-brand' href='#'><i class='fa-solid fa-dragon'></i> StatRift Admin</a>
            <div class='collapse navbar-collapse'>
                <ul class='navbar-nav ms-auto'>
                    <li class='nav-item'>
                        <a class='nav-link' href='/index.php'><i class='fa-solid fa-house me-1'></i> Volver a la Web</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link text-danger' href='/CONTROLADORES/logout.php'><i class='fa-solid fa-power-off me-1'></i> Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-md-3 col-lg-2 p-3 admin-sidebar">
                <ul class="nav nav-pills flex-column" id="adminTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100 text-start active" id="resumen-tab" data-bs-toggle="pill" data-bs-target="#resumen" type="button" role="tab"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100 text-start" id="usuarios-tab" data-bs-toggle="pill" data-bs-target="#usuarios" type="button" role="tab"><i class="fa-solid fa-users me-2"></i> Jugadores</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100 text-start" id="publicaciones-tab" data-bs-toggle="pill" data-bs-target="#publicaciones" type="button" role="tab"><i class="fa-solid fa-comments me-2"></i> Comunidad</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100 text-start" id="campeones-tab" data-bs-toggle="pill" data-bs-target="#campeones" type="button" role="tab"><i class="fa-solid fa-mask me-2"></i> Campeones</button>
                    </li>
                </ul>
            </div>

            <!-- CONTENT -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="tab-content" id="adminTabsContent">
                    
                    <!-- TAB: DASHBOARD -->
                    <div class="tab-pane fade show active" id="resumen" role="tabpanel" tabindex="0">
                        <h3 class="text-gold mb-4">Resumen del Sistema</h3>
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <div class="stat-card">
                                    <i class="fa-solid fa-users fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['usuarios'] ?></div>
                                    <div class="text-light">Jugadores</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="stat-card">
                                    <i class="fa-solid fa-comments fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['publicaciones'] ?></div>
                                    <div class="text-light">Publicaciones</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="stat-card">
                                    <i class="fa-solid fa-mask fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['campeones'] ?></div>
                                    <div class="text-light">Campeones</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="stat-card">
                                    <i class="fa-solid fa-trophy fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['partidos'] ?></div>
                                    <div class="text-light">Partidos</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: USUARIOS -->
                    <div class="tab-pane fade" id="usuarios" role="tabpanel" tabindex="0">
                        <h3 class="text-gold mb-4">Gestión de Jugadores</h3>
                        <div class="bg-glass p-4 rounded-4">
                            <table class="table table-dark table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th><th>Nick</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($u = $usuarios->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $u['id'] ?></td>
                                        <td><strong class="text-gold"><?= htmlspecialchars($u['nick']) ?></strong></td>
                                        <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?></td>
                                        <td><?= htmlspecialchars($u['correo']) ?></td>
                                        <td><span class="badge bg-secondary"><?= $u['tipo'] ?></span></td>
                                        <td>
                                            <?php if($u['activo']==1): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($u['nick'] !== 'admin'): ?>
                                            <button class="btn btn-outline-danger btn-sm btn-delete-user" data-id="<?= $u['id'] ?>"><i class="fa-solid fa-trash"></i> Eliminar</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB: COMUNIDAD -->
                    <div class="tab-pane fade" id="publicaciones" role="tabpanel" tabindex="0">
                        <h3 class="text-gold mb-4">Moderación de Comunidad</h3>
                        <div class="bg-glass p-4 rounded-4">
                            <table class="table table-dark table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th><th>Autor</th><th>Título</th><th>Fecha</th><th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($p = $publicaciones->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $p['id'] ?></td>
                                        <td><strong class="text-gold"><?= htmlspecialchars($p['nick'] ?? 'Desconocido') ?></strong></td>
                                        <td><?= htmlspecialchars($p['titulo']) ?></td>
                                        <td><?= $p['fecha'] ?></td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm btn-delete-post" data-id="<?= $p['id'] ?>"><i class="fa-solid fa-trash"></i> Borrar</button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB: CAMPEONES -->
                    <div class="tab-pane fade" id="campeones" role="tabpanel" tabindex="0">
                        <h3 class="text-gold mb-4">Añadir Nuevo Campeón</h3>
                        <div class="bg-glass p-4 rounded-4" style="max-width: 600px;">
                            <form id="formAddChampion">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Nombre del Campeón</label>
                                    <input type="text" id="c_nombre" class="form-control bg-dark text-light border-secondary" required>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label text-muted">Habilidad Q</label>
                                        <input type="text" id="c_q" class="form-control bg-dark text-light border-secondary">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label text-muted">Habilidad W</label>
                                        <input type="text" id="c_w" class="form-control bg-dark text-light border-secondary">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label text-muted">Habilidad E</label>
                                        <input type="text" id="c_e" class="form-control bg-dark text-light border-secondary">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label text-muted">Habilidad R (Ultimate)</label>
                                        <input type="text" id="c_r" class="form-control bg-dark text-light border-secondary">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted">URL del Splash Art</label>
                                    <input type="url" id="c_foto" class="form-control bg-dark text-light border-secondary" placeholder="https://..." required>
                                </div>
                                <button type="submit" class="btn btn-gold w-100"><i class="fa-solid fa-plus me-2"></i>Registrar Campeón</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Borrar Publicación
        document.querySelectorAll('.btn-delete-post').forEach(btn => {
            btn.addEventListener('click', function() {
                if(!confirm('¿Seguro que quieres borrar esta publicación y sus comentarios?')) return;
                const id = this.getAttribute('data-id');
                const formData = new FormData();
                formData.append('accion', 'delete_post');
                formData.append('id', id);

                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: formData })
                .then(res => res.json()).then(data => {
                    if(data.success) location.reload();
                    else alert(data.error);
                });
            });
        });

        // Borrar Usuario
        document.querySelectorAll('.btn-delete-user').forEach(btn => {
            btn.addEventListener('click', function() {
                if(!confirm('¿Seguro que quieres expulsar a este usuario de StatRift?')) return;
                const id = this.getAttribute('data-id');
                const formData = new FormData();
                formData.append('accion', 'delete_user');
                formData.append('id', id);

                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: formData })
                .then(res => res.json()).then(data => {
                    if(data.success) location.reload();
                    else alert(data.error);
                });
            });
        });

        // Añadir Campeón
        document.getElementById('formAddChampion').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;

            const formData = new FormData();
            formData.append('accion', 'add_champion');
            formData.append('nombre', document.getElementById('c_nombre').value);
            formData.append('q', document.getElementById('c_q').value);
            formData.append('w', document.getElementById('c_w').value);
            formData.append('e', document.getElementById('c_e').value);
            formData.append('r', document.getElementById('c_r').value);
            formData.append('foto', document.getElementById('c_foto').value);

            fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: formData })
            .then(res => res.json()).then(data => {
                if(data.success) {
                    alert('Campeón añadido con éxito.');
                    document.getElementById('formAddChampion').reset();
                    btn.disabled = false;
                } else {
                    alert(data.error);
                    btn.disabled = false;
                }
            });
        });
    </script>
</body>
</html>
