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
$stats         = $modelo->get_stats();
$publicaciones = $modelo->get_publicaciones();
$usuarios      = $modelo->get_usuarios();
$campeones     = $modelo->get_campeones();
$partidos      = $modelo->get_partidos();
$equipos       = $modelo->get_equipos();
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
    <link rel="stylesheet" href="../CSS/style.css?v=1.8">
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
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
            cursor: pointer;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(197,160,89,0.25);
            border-color: rgba(197,160,89,0.7);
        }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: var(--gold-accent); }
    </style>
</head>
<body>
    <?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
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
                        <button class="nav-link w-100 text-start" id="usuarios-tab" data-bs-toggle="pill" data-bs-target="#usuarios" type="button" role="tab"><i class="fa-solid fa-users me-2"></i> Usuarios</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100 text-start" id="publicaciones-tab" data-bs-toggle="pill" data-bs-target="#publicaciones" type="button" role="tab"><i class="fa-solid fa-comments me-2"></i> Comunidad</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100 text-start" id="campeones-tab" data-bs-toggle="pill" data-bs-target="#campeones" type="button" role="tab"><i class="fa-solid fa-mask me-2"></i> Campeones</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100 text-start" id="partidos-tab" data-bs-toggle="pill" data-bs-target="#partidos" type="button" role="tab"><i class="fa-solid fa-trophy me-2"></i> Partidos</button>
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
                                <div class="stat-card" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('usuarios-tab')).show()">
                                    <i class="fa-solid fa-users fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['usuarios'] ?></div>
                                    <div class="text-light">Usuarios</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="stat-card" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('publicaciones-tab')).show()">
                                    <i class="fa-solid fa-comments fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['publicaciones'] ?></div>
                                    <div class="text-light">Publicaciones</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="stat-card" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('campeones-tab')).show()">
                                    <i class="fa-solid fa-mask fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['campeones'] ?></div>
                                    <div class="text-light">Campeones</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="stat-card" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('partidos-tab')).show()">
                                    <i class="fa-solid fa-trophy fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['partidos'] ?></div>
                                    <div class="text-light">Partidos</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: USUARIOS -->
                    <div class="tab-pane fade" id="usuarios" role="tabpanel" tabindex="0">
                        <h3 class="text-gold mb-4">Gestión de Usuarios</h3>
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
                        <h3 class="text-gold mb-4">Gestión de Campeones</h3>

                        <!-- Lista de campeones -->
                        <div class="bg-glass p-4 rounded-4 mb-4">
                            <h5 class="text-muted mb-3">Campeones registrados</h5>
                            <table class="table table-dark table-hover align-middle">
                                <thead>
                                    <tr><th>ID</th><th>Nombre</th><th>Rol</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach($campeones as $c): ?>
                                    <tr>
                                        <td><?= $c['id'] ?></td>
                                        <td>
                                            <?php if($c['foto']): ?>
                                                <img src="<?= htmlspecialchars($c['foto']) ?>" alt="" style="width:32px;height:32px;object-fit:cover;border-radius:4px;margin-right:8px" onerror="this.style.display='none'">
                                            <?php endif; ?>
                                            <strong class="text-gold"><?= htmlspecialchars($c['nombre']) ?></strong>
                                        </td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($c['rol'] ?? '—') ?></span></td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm btn-delete-champion" data-id="<?= $c['id'] ?>" data-nombre="<?= htmlspecialchars($c['nombre']) ?>">
                                                <i class="fa-solid fa-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Formulario añadir -->
                        <div class="bg-glass p-4 rounded-4" style="max-width: 600px;">
                            <h5 class="text-muted mb-3">Añadir nuevo campeón</h5>
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

                    <!-- TAB: PARTIDOS -->
                    <div class="tab-pane fade" id="partidos" role="tabpanel" tabindex="0">
                        <h3 class="text-gold mb-4">Gestión de Partidos</h3>

                        <!-- Lista de partidos -->
                        <div class="bg-glass p-4 rounded-4 mb-4">
                            <h5 class="text-muted mb-3">Partidos registrados</h5>
                            <?php if(empty($partidos)): ?>
                                <p class="text-muted text-center py-3"><i class="fa-solid fa-calendar-xmark d-block fs-2 mb-2"></i>No hay partidos registrados.</p>
                            <?php else: ?>
                            <table class="table table-dark table-hover align-middle">
                                <thead>
                                    <tr><th>ID</th><th>Fecha</th><th>Equipo 1</th><th>Equipo 2</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach($partidos as $pt): ?>
                                    <tr>
                                        <td><?= $pt['id'] ?></td>
                                        <td><?= htmlspecialchars($pt['fecha']) ?></td>
                                        <td><strong class="text-gold"><?= htmlspecialchars($pt['equipo1']) ?></strong></td>
                                        <td><strong class="text-gold"><?= htmlspecialchars($pt['equipo2']) ?></strong></td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm btn-delete-partido" data-id="<?= $pt['id'] ?>">
                                                <i class="fa-solid fa-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>

                        <!-- Formulario añadir partido -->
                        <div class="bg-glass p-4 rounded-4" style="max-width: 540px;">
                            <h5 class="text-muted mb-3">Añadir nuevo partido</h5>
                            <form id="formAddPartido">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Fecha del partido</label>
                                    <input type="date" id="p_fecha" class="form-control bg-dark text-light border-secondary" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Equipo local</label>
                                    <select id="p_equipo1" class="form-select bg-dark text-light border-secondary" required>
                                        <option value="">— Seleccionar —</option>
                                        <?php foreach($equipos as $eq): ?>
                                        <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted">Equipo visitante</label>
                                    <select id="p_equipo2" class="form-select bg-dark text-light border-secondary" required>
                                        <option value="">— Seleccionar —</option>
                                        <?php foreach($equipos as $eq): ?>
                                        <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-gold w-100"><i class="fa-solid fa-plus me-2"></i>Crear Partido</button>
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
                const fd = new FormData();
                fd.append('accion', 'delete_post');
                fd.append('id', id);
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(d => { if(d.success) location.reload(); else alert(d.error); });
            });
        });

        // Borrar Usuario
        document.querySelectorAll('.btn-delete-user').forEach(btn => {
            btn.addEventListener('click', function() {
                if(!confirm('¿Seguro que quieres expulsar a este usuario de StatRift?')) return;
                const id = this.getAttribute('data-id');
                const fd = new FormData();
                fd.append('accion', 'delete_user');
                fd.append('id', id);
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(d => { if(d.success) location.reload(); else alert(d.error); });
            });
        });

        // Añadir Campeón
        document.getElementById('formAddChampion').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            const fd = new FormData();
            fd.append('accion', 'add_champion');
            fd.append('nombre', document.getElementById('c_nombre').value);
            fd.append('q', document.getElementById('c_q').value);
            fd.append('w', document.getElementById('c_w').value);
            fd.append('e', document.getElementById('c_e').value);
            fd.append('r', document.getElementById('c_r').value);
            fd.append('foto', document.getElementById('c_foto').value);
            fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(d => {
                if(d.success) { alert('Campeón añadido con éxito.'); location.reload(); }
                else { alert(d.error); btn.disabled = false; }
            });
        });

        // Eliminar Campeón
        document.querySelectorAll('.btn-delete-champion').forEach(btn => {
            btn.addEventListener('click', function() {
                const nombre = this.getAttribute('data-nombre');
                if(!confirm('¿Eliminar al campeón "' + nombre + '"? Esta acción no se puede deshacer.')) return;
                const fd = new FormData();
                fd.append('accion', 'delete_champion');
                fd.append('id', this.getAttribute('data-id'));
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(d => {
                    if(d.success) location.reload();
                    else alert(d.error);
                });
            });
        });

        // Añadir Partido
        document.getElementById('formAddPartido').addEventListener('submit', function(e) {
            e.preventDefault();
            const e1 = document.getElementById('p_equipo1').value;
            const e2 = document.getElementById('p_equipo2').value;
            if(e1 === e2) { alert('Los dos equipos no pueden ser el mismo.'); return; }
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            const fd = new FormData();
            fd.append('accion', 'add_partido');
            fd.append('fecha', document.getElementById('p_fecha').value);
            fd.append('id_equipo1', e1);
            fd.append('id_equipo2', e2);
            fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(d => {
                if(d.success) { alert('Partido creado con éxito.'); location.reload(); }
                else { alert(d.error); btn.disabled = false; }
            });
        });

        // Eliminar Partido
        document.querySelectorAll('.btn-delete-partido').forEach(btn => {
            btn.addEventListener('click', function() {
                if(!confirm('¿Eliminar este partido? Se borrarán también sus resultados.')) return;
                const fd = new FormData();
                fd.append('accion', 'delete_partido');
                fd.append('id', this.getAttribute('data-id'));
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(d => {
                    if(d.success) location.reload();
                    else alert(d.error);
                });
            });
        });
    </script>
</body>
</html>
