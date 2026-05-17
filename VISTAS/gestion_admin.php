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

$modelo        = new modelo_admin();
$stats         = $modelo->get_stats();
$publicaciones = $modelo->get_publicaciones();
$usuarios      = $modelo->get_usuarios();
$campeones     = $modelo->get_campeones();
$partidos      = $modelo->get_partidos();
$equipos       = $modelo->get_equipos();
$ligas         = $modelo->get_ligas();
$todos_equipos = $modelo->get_todos_equipos_admin();
$objetos_admin = $modelo->get_objetos_admin();
$runas_admin   = $modelo->get_runas_admin();
$runas_camino  = $modelo->get_runas_por_camino();
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
    <link rel="stylesheet" href="../CSS/style.css?v=1.9">
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
        .roster-row { background: rgba(255,255,255,0.03); border-radius: 8px; padding: 0.5rem 0.75rem; margin-bottom: 0.4rem; }
        .roster-pos-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gold-accent); min-width: 70px; }
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
                        <button class="nav-link w-100 text-start" id="equipos-tab" data-bs-toggle="pill" data-bs-target="#equipos" type="button" role="tab"><i class="fa-solid fa-shield-halved me-2"></i> Equipos</button>
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
                            <div class="col-md-2 col-sm-4 mb-4">
                                <div class="stat-card" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('usuarios-tab')).show()">
                                    <i class="fa-solid fa-users fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['usuarios'] ?></div>
                                    <div class="text-light">Usuarios</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-4">
                                <div class="stat-card" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('publicaciones-tab')).show()">
                                    <i class="fa-solid fa-comments fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['publicaciones'] ?></div>
                                    <div class="text-light">Publicaciones</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-4">
                                <div class="stat-card" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('campeones-tab')).show()">
                                    <i class="fa-solid fa-mask fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['campeones'] ?></div>
                                    <div class="text-light">Campeones</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-4">
                                <div class="stat-card" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('equipos-tab')).show()">
                                    <i class="fa-solid fa-shield-halved fs-2 text-muted mb-2"></i>
                                    <div class="stat-number"><?= $stats['equipos'] ?></div>
                                    <div class="text-light">Equipos</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 mb-4">
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
                        <div id="alerta-usuarios" class="alert mt-2" style="display:none;"></div>
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
                        <div id="alerta-publicaciones" class="alert mt-2" style="display:none;"></div>
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
                        <div id="alerta-campeones" class="alert mt-2" style="display:none;"></div>

                        <!-- Lista -->
                        <div class="bg-glass p-4 rounded-4 mb-4">
                            <h5 class="text-muted mb-3">Campeones registrados</h5>
                            <table class="table tabla-admin table-dark table-hover align-middle">
                                <thead>
                                    <tr><th>ID</th><th>Nombre</th><th>Rol</th><th class="text-center">Acciones</th></tr>
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
                                        <td class="acciones text-center text-nowrap">
                                            <button class="btn btn-outline-warning btn-sm me-1 btn-edit-champion" data-id="<?= $c['id'] ?>"><i class="fa-solid fa-pen"></i> Editar</button>
                                            <button class="btn btn-outline-danger btn-sm me-1 btn-delete-champion" data-id="<?= $c['id'] ?>" data-nombre="<?= htmlspecialchars($c['nombre']) ?>"><i class="fa-solid fa-trash"></i> Eliminar</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Formulario añadir -->
                        <div class="bg-glass p-4 rounded-4">
                            <h5 class="text-muted mb-3">Añadir nuevo campeón</h5>
                            <form id="formAddChampion">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Nombre del Campeón <span class="text-danger">*</span></label>
                                    <input type="text" id="c_nombre" class="form-control bg-dark text-light border-secondary" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">URL del Splash Art <span class="text-danger">*</span></label>
                                    <input type="url" id="c_foto" class="form-control bg-dark text-light border-secondary" placeholder="https://..." required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Rol</label>
                                    <select id="c_rol" class="form-select bg-dark text-light border-secondary">
                                        <option value="">— Sin especificar —</option>
                                        <option value="Top">Top</option>
                                        <option value="Jungla">Jungla</option>
                                        <option value="Mid">Mid</option>
                                        <option value="ADC">ADC</option>
                                        <option value="Support">Support</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Análisis / Descripción</label>
                                    <textarea id="c_descripcion" class="form-control bg-dark text-light border-secondary" rows="3" placeholder="Análisis técnico del campeón..."></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3"><label class="form-label text-muted">Habilidad Q</label><input type="text" id="c_q" class="form-control bg-dark text-light border-secondary"></div>
                                    <div class="col-6 mb-3"><label class="form-label text-muted">Habilidad W</label><input type="text" id="c_w" class="form-control bg-dark text-light border-secondary"></div>
                                    <div class="col-6 mb-3"><label class="form-label text-muted">Habilidad E</label><input type="text" id="c_e" class="form-control bg-dark text-light border-secondary"></div>
                                    <div class="col-6 mb-3"><label class="form-label text-muted">Habilidad R (Ultimate)</label><input type="text" id="c_r" class="form-control bg-dark text-light border-secondary"></div>
                                </div>

                                <!-- Build opcional -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-gold-outline w-100" data-bs-toggle="collapse" data-bs-target="#colapseBuild" aria-expanded="false">
                                        <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Añadir build (opcional)
                                    </button>
                                </div>
                                <div class="collapse" id="colapseBuild">
                                    <div class="border border-secondary rounded-3 p-3 mb-3">
                                        <h6 class="text-gold mb-3">Datos de la build</h6>
                                        <div class="mb-2">
                                            <label class="form-label text-muted small">Nombre de la build</label>
                                            <input type="text" id="b_nombre" class="form-control bg-dark text-light border-secondary form-control-sm" placeholder="Ej: Asesino Burst">
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-4"><label class="form-label text-muted small">Popularidad (%)</label><input type="number" id="b_popularidad" class="form-control bg-dark text-light border-secondary form-control-sm" value="80" min="0" max="100"></div>
                                            <div class="col-4"><label class="form-label text-muted small">Win Rate (%)</label><input type="number" id="b_winrate" class="form-control bg-dark text-light border-secondary form-control-sm" value="50.0" step="0.1" min="0" max="100"></div>
                                            <div class="col-4"><label class="form-label text-muted small">Total partidas</label><input type="number" id="b_matches" class="form-control bg-dark text-light border-secondary form-control-sm" value="10000" min="0"></div>
                                        </div>
                                        <h6 class="text-gold mb-2">Objetos (hasta 6)</h6>
                                        <?php for($slot = 1; $slot <= 6; $slot++): ?>
                                        <div class="row g-2 mb-2 align-items-center">
                                            <div class="col-1 text-muted small text-center"><?= $slot ?></div>
                                            <div class="col-5">
                                                <select name="obj_id[]" class="form-select bg-dark text-light border-secondary form-select-sm">
                                                    <option value="">— Objeto —</option>
                                                    <?php foreach($objetos_admin as $obj): ?>
                                                    <option value="<?= $obj['id'] ?>"><?= htmlspecialchars($obj['nombre']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <select name="obj_fase[]" class="form-select bg-dark text-light border-secondary form-select-sm">
                                                    <option value="starter">Starter</option>
                                                    <option value="early">Early</option>
                                                    <option value="core" selected>Core</option>
                                                    <option value="full">Full</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <input type="number" name="obj_orden[]" value="<?= $slot ?>" min="1" max="6" class="form-control bg-dark text-light border-secondary form-control-sm">
                                            </div>
                                        </div>
                                        <?php endfor; ?>
                                        <h6 class="text-gold mb-2 mt-3"><i class="fa-solid fa-bolt me-1"></i>Runas</h6>
                                        <div id="add-runas-container">
                                            <!-- generarRunasBlockHtml('add', 8000, 8100) lo rellena en init -->
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-gold w-100"><i class="fa-solid fa-plus me-2"></i>Registrar Campeón</button>
                            </form>
                        </div>
                    </div>

                    <!-- TAB: EQUIPOS -->
                    <div class="tab-pane fade" id="equipos" role="tabpanel" tabindex="0">
                        <h3 class="text-gold mb-4">Gestión de Equipos</h3>
                        <div id="alerta-equipos" class="alert mt-2" style="display:none;"></div>

                        <!-- Tabla -->
                        <div class="bg-glass p-4 rounded-4 mb-4">
                            <h5 class="text-muted mb-3">Equipos registrados</h5>
                            <?php if(empty($todos_equipos)): ?>
                                <p class="text-muted text-center py-3"><i class="fa-solid fa-shield-halved d-block fs-2 mb-2"></i>No hay equipos registrados.</p>
                            <?php else: ?>
                            <table class="table tabla-admin table-dark table-hover align-middle">
                                <thead>
                                    <tr><th>ID</th><th>Nombre</th><th>Liga</th><th>Ranking GPR</th><th class="text-center">Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach($todos_equipos as $eq): ?>
                                    <tr>
                                        <td><?= $eq['id'] ?></td>
                                        <td><strong class="text-gold"><?= htmlspecialchars($eq['nombre']) ?></strong></td>
                                        <td><?= htmlspecialchars($eq['nombre_liga'] ?? '—') ?></td>
                                        <td><?= intval($eq['ranking']) ?></td>
                                        <td class="acciones text-center text-nowrap">
                                            <button class="btn btn-outline-warning btn-sm me-1 btn-edit-equipo" data-id="<?= $eq['id'] ?>"><i class="fa-solid fa-pen"></i> Editar</button>
                                            <button class="btn btn-outline-danger btn-sm me-1 btn-delete-equipo" data-id="<?= $eq['id'] ?>" data-nombre="<?= htmlspecialchars($eq['nombre']) ?>"><i class="fa-solid fa-trash"></i> Eliminar</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>

                        <!-- Formulario añadir equipo -->
                        <div class="bg-glass p-4 rounded-4">
                            <h5 class="text-muted mb-3">Añadir nuevo equipo</h5>
                            <form id="formAddEquipo">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" id="eq_nombre" class="form-control bg-dark text-light border-secondary" required maxlength="50">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Liga <span class="text-danger">*</span></label>
                                    <select id="eq_liga" class="form-select bg-dark text-light border-secondary" required>
                                        <option value="">— Seleccionar —</option>
                                        <?php foreach($ligas as $lg): ?>
                                        <option value="<?= $lg['id'] ?>"><?= htmlspecialchars($lg['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">URL del Logo</label>
                                    <input type="text" id="eq_logo" class="form-control bg-dark text-light border-secondary" placeholder="https://..." maxlength="300">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Descripción</label>
                                    <textarea id="eq_descripcion" class="form-control bg-dark text-light border-secondary" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Ranking GPR</label>
                                    <input type="number" id="eq_ranking" class="form-control bg-dark text-light border-secondary" value="0" min="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">URL Video Highlights (YouTube embed)</label>
                                    <input type="text" id="eq_video" class="form-control bg-dark text-light border-secondary" placeholder="https://www.youtube.com/embed/..." maxlength="300">
                                </div>

                                <!-- Roster inicial -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-gold-outline w-100" data-bs-toggle="collapse" data-bs-target="#colapseRoster" aria-expanded="false">
                                        <i class="fa-solid fa-users me-2"></i>Roster inicial (opcional)
                                    </button>
                                </div>
                                <div class="collapse" id="colapseRoster">
                                    <div class="border border-secondary rounded-3 p-3 mb-3">
                                        <p class="text-muted small mb-3">Si nombre y nick están vacíos, esa posición no se inserta.</p>
                                        <?php foreach(['Top','Jungla','Mid','ADC','Support','Coach'] as $pos): ?>
                                        <div class="roster-row d-flex align-items-center gap-2 mb-2">
                                            <span class="roster-pos-label"><?= $pos ?></span>
                                            <input type="text" id="eq_r_nombre_<?= $pos ?>" class="form-control bg-dark text-light border-secondary form-control-sm" placeholder="Nombre real">
                                            <input type="text" id="eq_r_nick_<?= $pos ?>" class="form-control bg-dark text-light border-secondary form-control-sm" placeholder="Nick">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-gold w-100"><i class="fa-solid fa-plus me-2"></i>Añadir Equipo</button>
                            </form>
                        </div>
                    </div>

                    <!-- TAB: PARTIDOS -->
                    <div class="tab-pane fade" id="partidos" role="tabpanel" tabindex="0">
                        <h3 class="text-gold mb-4">Gestión de Partidos</h3>
                        <div id="alerta-partidos" class="alert mt-2" style="display:none;"></div>

                        <!-- Lista -->
                        <div class="bg-glass p-4 rounded-4 mb-4">
                            <h5 class="text-muted mb-3">Partidos registrados</h5>
                            <?php if(empty($partidos)): ?>
                                <p class="text-muted text-center py-3"><i class="fa-solid fa-calendar-xmark d-block fs-2 mb-2"></i>No hay partidos registrados.</p>
                            <?php else: ?>
                            <table class="table tabla-admin table-dark table-hover align-middle">
                                <thead>
                                    <tr><th>ID</th><th>Fecha</th><th>Equipo 1</th><th>Equipo 2</th><th class="text-center">Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach($partidos as $pt): ?>
                                    <tr>
                                        <td><?= $pt['id'] ?></td>
                                        <td><?= htmlspecialchars($pt['fecha']) ?></td>
                                        <td><strong class="text-gold"><?= htmlspecialchars($pt['equipo1']) ?></strong></td>
                                        <td><strong class="text-gold"><?= htmlspecialchars($pt['equipo2']) ?></strong></td>
                                        <td class="acciones text-center text-nowrap">
                                            <button class="btn btn-outline-warning btn-sm me-1 btn-edit-partido" data-id="<?= $pt['id'] ?>"><i class="fa-solid fa-pen"></i> Editar</button>
                                            <button class="btn btn-outline-danger btn-sm me-1 btn-delete-partido" data-id="<?= $pt['id'] ?>"><i class="fa-solid fa-trash"></i> Eliminar</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>

                        <!-- Formulario añadir partido -->
                        <div class="bg-glass p-4 rounded-4" style="max-width:540px;">
                            <h5 class="text-muted mb-3">Añadir nuevo partido</h5>
                            <form id="formAddPartido">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Liga</label>
                                    <select id="p_liga" class="form-select bg-dark text-light border-secondary" required>
                                        <option value="">— Seleccionar liga —</option>
                                        <?php foreach($ligas as $lg): ?>
                                        <option value="<?= $lg['id'] ?>"><?= htmlspecialchars($lg['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Equipo local</label>
                                    <select id="p_equipo1" class="form-select bg-dark text-light border-secondary" required disabled>
                                        <option value="">— Selecciona liga primero —</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Equipo visitante</label>
                                    <select id="p_equipo2" class="form-select bg-dark text-light border-secondary" required disabled>
                                        <option value="">— Selecciona liga primero —</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted">Fecha del partido</label>
                                    <input type="date" id="p_fecha" class="form-control bg-dark text-light border-secondary" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                <button type="submit" class="btn btn-gold w-100"><i class="fa-solid fa-plus me-2"></i>Crear Partido</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE EDICIÓN -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark border border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold" id="modalEditarTitle">Editar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalEditarBody"></div>
                <div id="alerta-modal" class="alert mx-3 mb-0" style="display:none;"></div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-gold" id="btnGuardarEdicion"><i class="fa-solid fa-save me-2"></i>Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Datos PHP para uso en JS
        window._ligas        = <?= json_encode($ligas) ?>;
        window._equipos      = <?= json_encode($equipos) ?>;
        window._objetos      = <?= json_encode($objetos_admin) ?>;
        window._runas        = <?= json_encode($runas_admin) ?>;
        window._runasCamino  = <?= json_encode($runas_camino) ?>;

        // Mapa camino → nombre y emoji para labels
        const RUNE_PATHS = {
            8000: 'Precisión',
            8100: 'Dominación',
            8200: 'Hechicería',
            8300: 'Inspiración',
            8400: 'Resolución'
        };
        const PATH_ICON = { 8000: '⚔️', 8100: '🗡️', 8200: '✨', 8300: '💡', 8400: '🛡️' };
        const PATH_IDS  = [8000, 8100, 8200, 8300, 8400];

        // Lookup id de runa → { path_id, tipo, nombre }
        const RUNAS_BY_ID = {};
        Object.keys(window._runasCamino || {}).forEach(function(pid) {
            (window._runasCamino[pid] || []).forEach(function(r) {
                RUNAS_BY_ID[r.id] = { path_id: parseInt(pid) || null, tipo: r.tipo, nombre: r.nombre };
            });
        });

        // Renderiza un selector de camino (path)
        function pathSelectHtml(idSel, valorSel) {
            let html = '<select id="' + idSel + '" class="form-select bg-dark text-light border-secondary form-select-sm select-path">';
            PATH_IDS.forEach(function(pid) {
                const sel = (parseInt(valorSel) === pid) ? ' selected' : '';
                html += '<option value="' + pid + '"' + sel + '>' + PATH_ICON[pid] + ' ' + RUNE_PATHS[pid] + '</option>';
            });
            html += '</select>';
            return html;
        }

        // Genera el bloque completo de runas (primario + secundario)
        // prefix = 'add' o 'edit' (para IDs únicos)
        function generarRunasBlockHtml(prefix, primaryPath, secondaryPath) {
            const idPrim = prefix + '-select-primary-path';
            const idSec  = prefix + '-select-secondary-path';
            let html = '<div class="runa-context" data-runa-prefix="' + prefix + '">';

            html += '<div class="mb-2"><label class="form-label text-muted small fw-bold">Camino Primario</label>' +
                pathSelectHtml(idPrim, primaryPath || 8000) + '</div>';

            const labelsP = ['Keystone', 'Menor 1', 'Menor 2', 'Menor 3'];
            labelsP.forEach(function(lbl, i) {
                html += '<div class="mb-2"><label class="form-label text-muted small">' + lbl + '</label>' +
                    '<select name="runa_primaria[]" data-runa-slot="primaria-' + i + '" class="form-select bg-dark text-light border-secondary form-select-sm">' +
                    '<option value="">-- Selecciona runa --</option></select></div>';
            });

            html += '<hr class="border-secondary my-3">';
            html += '<div class="mb-2"><label class="form-label text-muted small fw-bold">Camino Secundario</label>' +
                pathSelectHtml(idSec, secondaryPath || 8100) + '</div>';

            const labelsS = ['Runa sec. 1', 'Runa sec. 2'];
            labelsS.forEach(function(lbl, i) {
                html += '<div class="mb-2"><label class="form-label text-muted small">' + lbl + '</label>' +
                    '<select name="runa_secundaria[]" data-runa-slot="secundaria-' + i + '" class="form-select bg-dark text-light border-secondary form-select-sm">' +
                    '<option value="">-- Selecciona runa --</option></select></div>';
            });

            html += '</div>';
            return html;
        }

        // Rellena los selects de runas según el path. preseleccion = array de ids
        function rellenarSlotsRunas(container, tipoBloque, pathId, preseleccion) {
            const runas = (window._runasCamino[pathId] || []);
            container.querySelectorAll('select[data-runa-slot^="' + tipoBloque + '-"]').forEach(function(sel, i) {
                let listaFiltrada;
                if (tipoBloque === 'primaria') {
                    listaFiltrada = runas.filter(function(r) { return i === 0 ? r.tipo === 'Primaria' : r.tipo === 'Secundaria'; });
                } else {
                    listaFiltrada = runas.filter(function(r) { return r.tipo === 'Secundaria'; });
                }
                const seleccion = preseleccion ? preseleccion[i] : null;
                let html = '<option value="">-- Selecciona runa --</option>';
                listaFiltrada.forEach(function(r) {
                    const s = (seleccion && parseInt(seleccion) === parseInt(r.id)) ? ' selected' : '';
                    html += '<option value="' + r.id + '"' + s + '>' + esc(r.nombre) + '</option>';
                });
                sel.innerHTML = html;
            });
        }

        // Sincroniza el camino secundario para que no pueda ser igual al primario
        function sincronizarCaminoSecundario(container) {
            const prim = container.querySelector('#' + container.dataset.runaPrefix + '-select-primary-path');
            const sec  = container.querySelector('#' + container.dataset.runaPrefix + '-select-secondary-path');
            if (!prim || !sec) return;
            Array.from(sec.options).forEach(function(opt) {
                opt.disabled = (opt.value === prim.value);
            });
            if (sec.value === prim.value) {
                const libre = Array.from(sec.options).find(function(o) { return !o.disabled && o.value; });
                if (libre) sec.value = libre.value;
            }
        }

        // Conecta los listeners y rellena los slots iniciales
        function inicializarRunasBlock(container, preselecciones) {
            const prefix = container.dataset.runaPrefix;
            const prim = document.getElementById(prefix + '-select-primary-path');
            const sec  = document.getElementById(prefix + '-select-secondary-path');
            if (!prim || !sec) return;

            sincronizarCaminoSecundario(container);
            rellenarSlotsRunas(container, 'primaria',   prim.value, preselecciones ? preselecciones.primaria   : null);
            rellenarSlotsRunas(container, 'secundaria', sec.value,  preselecciones ? preselecciones.secundaria : null);

            prim.addEventListener('change', function() {
                sincronizarCaminoSecundario(container);
                rellenarSlotsRunas(container, 'primaria', this.value);
                // Si el secundario quedó vacío tras la sincronización, repoblar también
                rellenarSlotsRunas(container, 'secundaria', sec.value);
            });
            sec.addEventListener('change', function() {
                rellenarSlotsRunas(container, 'secundaria', this.value);
            });
        }

        // Dado un build.runas (array {id_runa,...}) y los path ids, clasifica las runas en los slots
        function clasificarRunas(buildRunas, primaryPath, secondaryPath) {
            const res = { primaria: [null, null, null, null], secundaria: [null, null] };
            let minIdx = 1, secIdx = 0;
            (buildRunas || []).forEach(function(r) {
                const meta = RUNAS_BY_ID[r.id_runa];
                if (!meta) return;
                if (meta.tipo === 'Primaria' && meta.path_id === parseInt(primaryPath)) {
                    res.primaria[0] = r.id_runa;
                } else if (meta.tipo === 'Secundaria' && meta.path_id === parseInt(primaryPath)) {
                    if (minIdx <= 3) res.primaria[minIdx++] = r.id_runa;
                } else if (meta.tipo === 'Secundaria' && meta.path_id === parseInt(secondaryPath)) {
                    if (secIdx <= 1) res.secundaria[secIdx++] = r.id_runa;
                }
            });
            return res;
        }

        // ─── HELPERS ──────────────────────────────────────────────────────
        function esc(str) {
            const d = document.createElement('div');
            d.textContent = str ?? '';
            return d.innerHTML;
        }

        function mostrarAlerta(idDiv, mensaje, tipo) {
            tipo = tipo || 'danger';
            const el = document.getElementById(idDiv);
            if (!el) return;
            el.className = 'alert alert-' + tipo + ' mt-2';
            el.textContent = mensaje;
            el.style.display = 'block';
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            setTimeout(function() { el.style.display = 'none'; }, 6000);
        }

        function accionExitosa(idAlerta, mensaje, hash) {
            let cuenta = 5;
            const el = document.getElementById(idAlerta);
            el.className = 'alert alert-success mt-2';
            el.textContent = mensaje + ` Actualizando en ${cuenta}...`;
            el.style.display = 'block';
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            const intervalo = setInterval(() => {
                cuenta--;
                if (cuenta <= 0) {
                    clearInterval(intervalo);
                    // Forzar recarga real con timestamp para evitar caché del navegador
                    const destino = window.location.pathname
                        + '?t=' + Date.now()
                        + (hash || window.location.hash || '');
                    window.location.replace(destino);
                } else {
                    el.textContent = mensaje + ` Actualizando en ${cuenta}...`;
                }
            }, 1000);
        }

        function accionError(idAlerta, mensaje) {
            const el = document.getElementById(idAlerta);
            el.className = 'alert alert-danger mt-2';
            el.textContent = mensaje;
            el.style.display = 'block';
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            // NO recargar en error
        }

        // Mantener hash al cambiar de tab
        document.querySelectorAll('[data-bs-toggle="pill"]').forEach(function(tabEl) {
            tabEl.addEventListener('shown.bs.tab', function() {
                const target = this.getAttribute('data-bs-target').replace('#', '');
                history.replaceState(null, '', window.location.pathname + '#seccion-' + target);
            });
        });

        // ─── PUBLICACIONES ────────────────────────────────────────────────
        document.querySelectorAll('.btn-delete-post').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('¿Borrar esta publicación y sus comentarios?')) return;
                const fd = new FormData();
                fd.append('accion', 'delete_post');
                fd.append('id', this.dataset.id);
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.success) { accionExitosa('alerta-publicaciones', 'Publicación eliminada correctamente.'); }
                        else { accionError('alerta-publicaciones', d.error || 'Error desconocido.'); }
                    })
                    .catch(function() { accionError('alerta-publicaciones', 'Error de conexión. Inténtalo de nuevo.'); });
            });
        });

        // ─── USUARIOS ─────────────────────────────────────────────────────
        document.querySelectorAll('.btn-delete-user').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('¿Eliminar a este usuario de StatRift?')) return;
                const fd = new FormData();
                fd.append('accion', 'delete_user');
                fd.append('id', this.dataset.id);
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.success) { accionExitosa('alerta-usuarios', 'Usuario eliminado correctamente.'); }
                        else { accionError('alerta-usuarios', d.error || 'Error desconocido.'); }
                    })
                    .catch(function() { accionError('alerta-usuarios', 'Error de conexión. Inténtalo de nuevo.'); });
            });
        });

        // ─── CAMPEONES ────────────────────────────────────────────────────
        document.getElementById('formAddChampion').addEventListener('submit', function(e) {
            e.preventDefault();
            const nombre = document.getElementById('c_nombre').value.trim();
            const foto   = document.getElementById('c_foto').value.trim();
            if (!nombre) { mostrarAlerta('alerta-campeones', 'El nombre del campeón es obligatorio.', 'warning'); return; }
            if (!foto)   { mostrarAlerta('alerta-campeones', 'La URL del splash art es obligatoria.', 'warning'); return; }
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            const fd = new FormData();
            fd.append('accion', 'add_champion');
            fd.append('nombre',      nombre);
            fd.append('foto',        foto);
            fd.append('rol',         document.getElementById('c_rol').value);
            fd.append('descripcion', document.getElementById('c_descripcion').value);
            fd.append('q',           document.getElementById('c_q').value);
            fd.append('w',           document.getElementById('c_w').value);
            fd.append('e',           document.getElementById('c_e').value);
            fd.append('r',           document.getElementById('c_r').value);
            const nombreBuild = document.getElementById('b_nombre').value.trim();
            if (nombreBuild) {
                let addCtx = document.getElementById('add-runas-container');
                // Self-healing: si el IIFE no llegó a inicializar el bloque,
                // hacerlo ahora antes de leer los selects.
                if (addCtx && !addCtx.querySelector('.runa-context') && typeof generarRunasBlockHtml === 'function') {
                    addCtx.innerHTML = generarRunasBlockHtml('add', 8000, 8100);
                    const inner = addCtx.querySelector('.runa-context');
                    if (inner) inicializarRunasBlock(inner, null);
                }
                const primPath = addCtx ? addCtx.querySelector('#add-select-primary-path') : null;
                const secPath  = addCtx ? addCtx.querySelector('#add-select-secondary-path') : null;
                fd.append('nombre_build',   nombreBuild);
                fd.append('popularidad',    document.getElementById('b_popularidad').value);
                fd.append('win_rate',       document.getElementById('b_winrate').value);
                fd.append('total_matches',  document.getElementById('b_matches').value);
                fd.append('primary_path',   primPath ? primPath.value : 8000);
                fd.append('secondary_path', secPath  ? secPath.value  : 8100);
                document.querySelectorAll('#formAddChampion select[name="obj_id[]"]').forEach(function(sel, i) {
                    fd.append('obj_id[]',    sel.value);
                    fd.append('obj_fase[]',  document.querySelectorAll('#formAddChampion select[name="obj_fase[]"]')[i].value);
                    fd.append('obj_orden[]', document.querySelectorAll('#formAddChampion input[name="obj_orden[]"]')[i].value);
                });
                if (addCtx) {
                    addCtx.querySelectorAll('select[name="runa_primaria[]"]').forEach(function(sel) {
                        fd.append('runa_primaria[]', sel.value);
                    });
                    addCtx.querySelectorAll('select[name="runa_secundaria[]"]').forEach(function(sel) {
                        fd.append('runa_secundaria[]', sel.value);
                    });
                }
            }
            fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.success) {
                        const w = d.warning ? ' Aviso: ' + d.warning : '';
                        accionExitosa('alerta-campeones', 'Campeón añadido con éxito.' + w);
                    } else { accionError('alerta-campeones', d.error || 'Error desconocido.'); btn.disabled = false; }
                })
                .catch(function() { accionError('alerta-campeones', 'Error de conexión. Inténtalo de nuevo.'); btn.disabled = false; });
        });

        document.querySelectorAll('.btn-delete-champion').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('¿Eliminar al campeón "' + this.dataset.nombre + '"? No se puede deshacer.')) return;
                const fd = new FormData();
                fd.append('accion', 'delete_champion');
                fd.append('id', this.dataset.id);
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.success) { accionExitosa('alerta-campeones', 'Campeón eliminado correctamente.'); }
                        else { accionError('alerta-campeones', d.error || 'Error.'); }
                    })
                    .catch(function() { accionError('alerta-campeones', 'Error de conexión.'); });
            });
        });

        function generarBuildHtml(build) {
            const prim = build ? parseInt(build.primary_path)   : 8000;
            const sec  = build ? parseInt(build.secondary_path) : 8100;

            let html = '<hr class="border-secondary my-3"><h6 class="text-gold mb-3">Build asociada</h6>';
            html += '<div class="mb-2"><label class="form-label text-muted small">Nombre de la build</label>' +
                '<input type="text" id="edit_b_nombre" class="form-control bg-dark text-light border-secondary form-control-sm" value="' + esc(build ? build.nombre_build : '') + '"></div>';
            html += '<div class="row g-2 mb-2">' +
                '<div class="col-4"><label class="form-label text-muted small">Popularidad (%)</label>' +
                '<input type="number" id="edit_b_popularidad" class="form-control bg-dark text-light border-secondary form-control-sm" value="' + (build ? build.popularidad : 80) + '" min="0" max="100"></div>' +
                '<div class="col-4"><label class="form-label text-muted small">Win Rate (%)</label>' +
                '<input type="number" id="edit_b_winrate" class="form-control bg-dark text-light border-secondary form-control-sm" value="' + (build ? build.win_rate : 50.0) + '" step="0.1" min="0" max="100"></div>' +
                '<div class="col-4"><label class="form-label text-muted small">Total partidas</label>' +
                '<input type="number" id="edit_b_matches" class="form-control bg-dark text-light border-secondary form-control-sm" value="' + (build ? build.total_matches : 10000) + '" min="0"></div>' +
                '</div>';

            html += '<h6 class="text-gold mb-2">Objetos (hasta 6)</h6>';
            const buildObjetos = build ? (build.objetos || []) : [];
            for (let slot = 0; slot < 6; slot++) {
                const obj = buildObjetos[slot] || null;
                html += '<div class="row g-2 mb-2 align-items-center">' +
                    '<div class="col-1 text-muted small text-center">' + (slot + 1) + '</div>' +
                    '<div class="col-5"><select name="edit_obj_id[]" class="form-select bg-dark text-light border-secondary form-select-sm">' +
                    '<option value="">— Objeto —</option>';
                (window._objetos || []).forEach(function(o) {
                    html += '<option value="' + o.id + '"' + (obj && obj.id_objeto == o.id ? ' selected' : '') + '>' + esc(o.nombre) + '</option>';
                });
                html += '</select></div>' +
                    '<div class="col-3"><select name="edit_obj_fase[]" class="form-select bg-dark text-light border-secondary form-select-sm">';
                ['starter','early','core','full'].forEach(function(f) {
                    const sel = obj ? (obj.fase === f ? ' selected' : '') : (f === 'core' ? ' selected' : '');
                    html += '<option value="' + f + '"' + sel + '>' + f.charAt(0).toUpperCase() + f.slice(1) + '</option>';
                });
                html += '</select></div>' +
                    '<div class="col-3"><input type="number" name="edit_obj_orden[]" value="' + (obj ? obj.orden : (slot + 1)) + '" min="1" max="6" class="form-control bg-dark text-light border-secondary form-control-sm"></div>' +
                    '</div>';
            }

            html += '<h6 class="text-gold mb-2 mt-3"><i class="fa-solid fa-bolt me-1"></i>Runas</h6>';
            html += '<div id="edit-runas-wrapper">' + generarRunasBlockHtml('edit', prim, sec) + '</div>';
            return html;
        }

        document.querySelectorAll('.btn-edit-champion').forEach(function(btn) {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                try {
                    const [res, buildRes] = await Promise.all([
                        fetch('/CONTROLADORES/api_admin.php?accion=get_campeon&id=' + id),
                        fetch('/CONTROLADORES/api_admin.php?accion=get_build_campeon&id_campeon=' + id)
                    ]);
                    const data      = await res.json();
                    const buildData = await buildRes.json();
                    if (!data.success) { mostrarAlerta('alerta-campeones', data.error || 'Error cargando campeón.', 'danger'); return; }
                    const c          = data.campeon;
                    const tieneBuild = (buildData && buildData.tiene_build === true && buildData.build);
                    const build      = tieneBuild ? buildData.build : null;
                    document.getElementById('modalEditarTitle').textContent = 'Editar Campeón';
                    document.getElementById('alerta-modal').style.display = 'none';
                    const roles = ['Top','Jungla','Mid','ADC','Support'];
                    document.getElementById('modalEditarBody').innerHTML =
                        '<input type="hidden" id="edit_id" value="' + esc(c.id) + '">' +
                        '<input type="hidden" id="edit_entidad" value="campeon">' +
                        '<div class="row g-2">' +
                        '<div class="col-md-8 mb-2"><label class="form-label text-muted small">Nombre</label>' +
                        '<input type="text" id="edit_nombre" class="form-control bg-dark text-light border-secondary" value="' + esc(c.nombre) + '"></div>' +
                        '<div class="col-md-4 mb-2"><label class="form-label text-muted small">Rol</label>' +
                        '<select id="edit_rol" class="form-select bg-dark text-light border-secondary">' +
                        '<option value="">— —</option>' +
                        roles.map(function(r){ return '<option value="'+r+'"'+(c.rol===r?' selected':'')+'>'+r+'</option>'; }).join('') +
                        '</select></div>' +
                        '<div class="col-12 mb-2"><label class="form-label text-muted small">URL Splash Art</label>' +
                        '<input type="url" id="edit_foto" class="form-control bg-dark text-light border-secondary" value="' + esc(c.foto||'') + '"></div>' +
                        '<div class="col-12 mb-2"><label class="form-label text-muted small">Descripción</label>' +
                        '<textarea id="edit_descripcion" class="form-control bg-dark text-light border-secondary" rows="3">' + esc(c.descripcion||'') + '</textarea></div>' +
                        '<div class="col-6 mb-2"><label class="form-label text-muted small">Q</label><input type="text" id="edit_q" class="form-control bg-dark text-light border-secondary" value="' + esc(c.q||'') + '"></div>' +
                        '<div class="col-6 mb-2"><label class="form-label text-muted small">W</label><input type="text" id="edit_w" class="form-control bg-dark text-light border-secondary" value="' + esc(c.w||'') + '"></div>' +
                        '<div class="col-6 mb-2"><label class="form-label text-muted small">E</label><input type="text" id="edit_e" class="form-control bg-dark text-light border-secondary" value="' + esc(c.e||'') + '"></div>' +
                        '<div class="col-6 mb-2"><label class="form-label text-muted small">R</label><input type="text" id="edit_r" class="form-control bg-dark text-light border-secondary" value="' + esc(c.r||'') + '"></div>' +
                        '</div>' +
                        generarBuildHtml(build);

                    // Inicializar bloque de runas del modal EDIT
                    const editCtx = document.querySelector('#edit-runas-wrapper .runa-context');
                    if (editCtx) {
                        let preseleccion = null;
                        if (tieneBuild) {
                            // Preferir clasificación que ya viene del backend; fallback al cliente
                            if (Array.isArray(build.runas_primarias) || Array.isArray(build.runas_secundarias)) {
                                preseleccion = {
                                    primaria:   build.runas_primarias   || [null, null, null, null],
                                    secundaria: build.runas_secundarias || [null, null]
                                };
                            } else {
                                const primPath = parseInt(build.primary_path);
                                const secPath  = parseInt(build.secondary_path);
                                preseleccion = clasificarRunas(build.runas, primPath, secPath);
                            }
                        }
                        inicializarRunasBlock(editCtx, preseleccion);
                    }

                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditar')).show();
                } catch(err) { mostrarAlerta('alerta-campeones', 'Error cargando datos del campeón.', 'danger'); }
            });
        });

        // ─── EQUIPOS ──────────────────────────────────────────────────────
        document.getElementById('p_liga').addEventListener('change', async function() {
            const s1 = document.getElementById('p_equipo1');
            const s2 = document.getElementById('p_equipo2');
            s1.innerHTML = '<option value="">— Selecciona equipo —</option>';
            s2.innerHTML = '<option value="">— Selecciona equipo —</option>';
            s1.disabled = true; s2.disabled = true;
            if (!this.value) return;
            const res  = await fetch('/CONTROLADORES/api_admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'accion=get_equipos_by_liga&id_liga=' + this.value
            });
            const data = await res.json();
            if (data.equipos && data.equipos.length > 0) {
                [s1, s2].forEach(function(sel) {
                    data.equipos.forEach(function(eq) { sel.innerHTML += '<option value="' + eq.id + '">' + esc(eq.nombre) + '</option>'; });
                });
                s1.disabled = false; s2.disabled = false;
            } else {
                s1.innerHTML = '<option value="">— Sin equipos en esta liga —</option>';
                s2.innerHTML = '<option value="">— Sin equipos en esta liga —</option>';
            }
        });

        document.getElementById('formAddEquipo').addEventListener('submit', function(e) {
            e.preventDefault();
            const nombre = document.getElementById('eq_nombre').value.trim();
            const liga   = document.getElementById('eq_liga').value;
            const logo   = document.getElementById('eq_logo').value.trim();
            if (!nombre) { mostrarAlerta('alerta-equipos', 'El nombre del equipo es obligatorio.', 'warning'); return; }
            if (!liga)   { mostrarAlerta('alerta-equipos', 'Debes seleccionar una liga.', 'warning'); return; }
            if (logo && !logo.startsWith('http')) { mostrarAlerta('alerta-equipos', 'La URL del logo debe comenzar por http.', 'warning'); return; }
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            const fd = new FormData();
            fd.append('accion', 'add_equipo');
            fd.append('nombre',      nombre);
            fd.append('id_liga',     liga);
            fd.append('logo',        logo);
            fd.append('descripcion', document.getElementById('eq_descripcion').value.trim());
            fd.append('ranking',     document.getElementById('eq_ranking').value || 0);
            fd.append('video',       document.getElementById('eq_video').value.trim());
            ['Top','Jungla','Mid','ADC','Support','Coach'].forEach(function(pos) {
                const n = document.getElementById('eq_r_nombre_' + pos);
                const k = document.getElementById('eq_r_nick_' + pos);
                if (n && k && n.value.trim() && k.value.trim()) {
                    fd.append('roster_nombre[]', n.value.trim());
                    fd.append('roster_nick[]',   k.value.trim());
                    fd.append('roster_pos[]',    pos);
                }
            });
            fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.success) { accionExitosa('alerta-equipos', 'Equipo añadido con éxito.'); }
                    else { accionError('alerta-equipos', d.error || 'Error desconocido.'); btn.disabled = false; }
                })
                .catch(function() { accionError('alerta-equipos', 'Error de conexión. Inténtalo de nuevo.'); btn.disabled = false; });
        });

        document.querySelectorAll('.btn-delete-equipo').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('¿Eliminar el equipo "' + this.dataset.nombre + '"? No se puede deshacer.')) return;
                const fd = new FormData();
                fd.append('accion', 'delete_equipo');
                fd.append('id', this.dataset.id);
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.success) { accionExitosa('alerta-equipos', 'Equipo eliminado correctamente.'); }
                        else { accionError('alerta-equipos', d.error || 'El equipo tiene partidos asociados. Elimina los partidos primero.'); }
                    })
                    .catch(function() { accionError('alerta-equipos', 'Error de conexión.'); });
            });
        });

        document.querySelectorAll('.btn-edit-equipo').forEach(function(btn) {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                try {
                    const res  = await fetch('/CONTROLADORES/api_admin.php?accion=get_equipo&id=' + id);
                    const data = await res.json();
                    if (!data.success) { mostrarAlerta('alerta-equipos', data.error || 'Error cargando equipo.', 'danger'); return; }
                    const eq = data.equipo;
                    document.getElementById('modalEditarTitle').textContent = 'Editar Equipo';
                    document.getElementById('alerta-modal').style.display = 'none';
                    const ligasOpts = window._ligas.map(function(l) {
                        return '<option value="' + l.id + '"' + (eq.id_liga == l.id ? ' selected' : '') + '>' + esc(l.nombre) + '</option>';
                    }).join('');
                    const roster = eq.roster || {};
                    const posiciones = ['Top','Jungla','Mid','ADC','Support','Coach'];
                    let rosterHtml = '<hr class="border-secondary my-3"><h6 class="text-gold mb-3"><i class="fa-solid fa-users me-2"></i>Roster del equipo</h6>' +
                        '<p class="text-muted small mb-3">Si nombre y nick están vacíos, esa posición se desactiva (no se elimina).</p>';
                    posiciones.forEach(function(pos) {
                        const r = roster[pos] || {};
                        rosterHtml += '<div class="roster-row d-flex flex-wrap align-items-center gap-2 mb-2">' +
                            '<input type="hidden" name="roster_id_' + pos + '" value="' + esc(r.id || '') + '">' +
                            '<span class="roster-pos-label">' + pos + '</span>' +
                            '<input type="text" name="roster_nombre_' + pos + '" class="form-control bg-dark text-light border-secondary form-control-sm" placeholder="Nombre real" style="flex:1;min-width:140px" value="' + esc(r.nombre || '') + '">' +
                            '<input type="text" name="roster_nick_' + pos + '" class="form-control bg-dark text-light border-secondary form-control-sm" placeholder="Nick" style="flex:1;min-width:120px" value="' + esc(r.nick || '') + '">' +
                            '<input type="text" name="roster_foto_url_' + pos + '" class="form-control bg-dark text-light border-secondary form-control-sm" placeholder="Foto URL" style="flex:2;min-width:200px" value="' + esc(r.foto_url || '') + '">' +
                            '</div>';
                    });

                    document.getElementById('modalEditarBody').innerHTML =
                        '<input type="hidden" id="edit_id" value="' + esc(eq.id) + '">' +
                        '<input type="hidden" id="edit_entidad" value="equipo">' +
                        '<div class="mb-2"><label class="form-label text-muted small">Nombre</label>' +
                        '<input type="text" id="edit_nombre" class="form-control bg-dark text-light border-secondary" value="' + esc(eq.nombre) + '"></div>' +
                        '<div class="mb-2"><label class="form-label text-muted small">Liga</label>' +
                        '<select id="edit_id_liga" class="form-select bg-dark text-light border-secondary"><option value="">— —</option>' + ligasOpts + '</select></div>' +
                        '<div class="mb-2"><label class="form-label text-muted small">URL Logo</label>' +
                        '<input type="text" id="edit_logo" class="form-control bg-dark text-light border-secondary" value="' + esc(eq.Logo||'') + '"></div>' +
                        '<div class="mb-2"><label class="form-label text-muted small">Descripción</label>' +
                        '<textarea id="edit_descripcion" class="form-control bg-dark text-light border-secondary" rows="2">' + esc(eq.descripcion||'') + '</textarea></div>' +
                        '<div class="row g-2">' +
                        '<div class="col-6 mb-2"><label class="form-label text-muted small">Ranking GPR</label>' +
                        '<input type="number" id="edit_ranking" class="form-control bg-dark text-light border-secondary" value="' + (eq.ranking||0) + '"></div>' +
                        '<div class="col-6 mb-2"><label class="form-label text-muted small">Video Highlights</label>' +
                        '<input type="text" id="edit_video" class="form-control bg-dark text-light border-secondary" value="' + esc(eq.video_highlights||'') + '"></div>' +
                        '</div>' +
                        rosterHtml;
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditar')).show();
                } catch(err) { mostrarAlerta('alerta-equipos', 'Error cargando datos del equipo.', 'danger'); }
            });
        });

        // ─── PARTIDOS ─────────────────────────────────────────────────────
        document.getElementById('formAddPartido').addEventListener('submit', function(e) {
            e.preventDefault();
            const fechaInput = document.getElementById('p_fecha');
            const hoy = new Date(); hoy.setHours(0,0,0,0);
            if (new Date(fechaInput.value) < hoy) { mostrarAlerta('alerta-partidos', 'La fecha del partido debe ser hoy o en el futuro.', 'warning'); return; }
            const e1 = document.getElementById('p_equipo1').value;
            const e2 = document.getElementById('p_equipo2').value;
            if (!e1 || !e2) { mostrarAlerta('alerta-partidos', 'Debes seleccionar ambos equipos.', 'warning'); return; }
            if (e1 === e2)  { mostrarAlerta('alerta-partidos', 'Los dos equipos deben ser diferentes.', 'warning'); return; }
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            const fd = new FormData();
            fd.append('accion', 'add_partido');
            fd.append('fecha', fechaInput.value);
            fd.append('id_equipo1', e1);
            fd.append('id_equipo2', e2);
            fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.success) { accionExitosa('alerta-partidos', 'Partido creado con éxito.'); }
                    else { accionError('alerta-partidos', d.error || 'Error desconocido.'); btn.disabled = false; }
                })
                .catch(function() { accionError('alerta-partidos', 'Error de conexión. Inténtalo de nuevo.'); btn.disabled = false; });
        });

        document.querySelectorAll('.btn-delete-partido').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('¿Eliminar este partido y sus resultados?')) return;
                const fd = new FormData();
                fd.append('accion', 'delete_partido');
                fd.append('id', this.dataset.id);
                fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.success) { accionExitosa('alerta-partidos', 'Partido eliminado correctamente.'); }
                        else { accionError('alerta-partidos', d.error || 'Error.'); }
                    })
                    .catch(function() { accionError('alerta-partidos', 'Error de conexión.'); });
            });
        });

        document.querySelectorAll('.btn-edit-partido').forEach(function(btn) {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                try {
                    const res  = await fetch('/CONTROLADORES/api_admin.php?accion=get_partido_admin&id=' + id);
                    const data = await res.json();
                    if (!data.success) { mostrarAlerta('alerta-partidos', data.error || 'Error cargando partido.', 'danger'); return; }
                    const pt = data.partido;
                    document.getElementById('modalEditarTitle').textContent = 'Editar Partido';
                    document.getElementById('alerta-modal').style.display = 'none';
                    const eqOpts1 = window._equipos.map(function(eq) {
                        return '<option value="' + eq.id + '"' + (eq.id == pt.id_equipo1 ? ' selected' : '') + '>' + esc(eq.nombre) + '</option>';
                    }).join('');
                    const eqOpts2 = window._equipos.map(function(eq) {
                        return '<option value="' + eq.id + '"' + (eq.id == pt.id_equipo2 ? ' selected' : '') + '>' + esc(eq.nombre) + '</option>';
                    }).join('');
                    document.getElementById('modalEditarBody').innerHTML =
                        '<input type="hidden" id="edit_id" value="' + esc(pt.id) + '">' +
                        '<input type="hidden" id="edit_entidad" value="partido">' +
                        '<div class="mb-2"><label class="form-label text-muted small">Fecha</label>' +
                        '<input type="date" id="edit_fecha" class="form-control bg-dark text-light border-secondary" value="' + esc(pt.fecha) + '"></div>' +
                        '<div class="row g-2 mb-2">' +
                        '<div class="col-6"><label class="form-label text-muted small">Equipo 1</label>' +
                        '<select id="edit_eq1" class="form-select bg-dark text-light border-secondary">' + eqOpts1 + '</select></div>' +
                        '<div class="col-6"><label class="form-label text-muted small">Equipo 2</label>' +
                        '<select id="edit_eq2" class="form-select bg-dark text-light border-secondary">' + eqOpts2 + '</select></div>' +
                        '</div>' +
                        '<div class="row g-2 mb-2">' +
                        '<div class="col-6"><label class="form-label text-muted small">Resultado eq. 1</label>' +
                        '<input type="number" id="edit_res1" class="form-control bg-dark text-light border-secondary" value="' + (pt.res1||0) + '" min="0"></div>' +
                        '<div class="col-6"><label class="form-label text-muted small">Resultado eq. 2</label>' +
                        '<input type="number" id="edit_res2" class="form-control bg-dark text-light border-secondary" value="' + (pt.res2||0) + '" min="0"></div>' +
                        '</div>' +
                        '<div class="mb-2"><label class="form-label text-muted small">URL del directo (Twitch)</label>' +
                        '<input type="text" id="edit_stream_url" class="form-control bg-dark text-light border-secondary" value="' + esc(pt.stream_url||'') + '" placeholder="https://player.twitch.tv/...">' +
                        '<div class="form-text text-muted small">Solo se mostrará el día del partido. Puedes añadirlo antes pero no será visible hasta ese día.</div></div>';
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditar')).show();
                } catch(err) { mostrarAlerta('alerta-partidos', 'Error cargando datos del partido.', 'danger'); }
            });
        });

        // ─── MODAL GUARDAR ────────────────────────────────────────────────
        document.getElementById('btnGuardarEdicion').addEventListener('click', async function() {
            const entidad = document.getElementById('edit_entidad').value;
            const id      = document.getElementById('edit_id').value;
            const fd = new FormData();
            fd.append('accion', 'update_' + entidad);
            fd.append('id', id);

            if (entidad === 'campeon') {
                fd.append('nombre',      document.getElementById('edit_nombre').value);
                fd.append('rol',         document.getElementById('edit_rol').value);
                fd.append('foto',        document.getElementById('edit_foto').value);
                fd.append('descripcion', document.getElementById('edit_descripcion').value);
                fd.append('q', document.getElementById('edit_q').value);
                fd.append('w', document.getElementById('edit_w').value);
                fd.append('e', document.getElementById('edit_e').value);
                fd.append('r', document.getElementById('edit_r').value);
                const bNombre = document.getElementById('edit_b_nombre');
                if (bNombre && bNombre.value.trim()) {
                    const editCtx = document.querySelector('#edit-runas-wrapper .runa-context');
                    const primPath = editCtx ? editCtx.querySelector('#edit-select-primary-path').value   : 8000;
                    const secPath  = editCtx ? editCtx.querySelector('#edit-select-secondary-path').value : 8100;
                    fd.append('nombre_build',   bNombre.value.trim());
                    fd.append('popularidad',    document.getElementById('edit_b_popularidad').value);
                    fd.append('win_rate',       document.getElementById('edit_b_winrate').value);
                    fd.append('total_matches',  document.getElementById('edit_b_matches').value);
                    fd.append('primary_path',   primPath);
                    fd.append('secondary_path', secPath);
                    document.querySelectorAll('select[name="edit_obj_id[]"]').forEach(function(sel, i) {
                        fd.append('obj_id[]',    sel.value);
                        fd.append('obj_fase[]',  document.querySelectorAll('select[name="edit_obj_fase[]"]')[i].value);
                        fd.append('obj_orden[]', document.querySelectorAll('input[name="edit_obj_orden[]"]')[i].value);
                    });
                    if (editCtx) {
                        editCtx.querySelectorAll('select[name="runa_primaria[]"]').forEach(function(sel) {
                            fd.append('runa_primaria[]', sel.value);
                        });
                        editCtx.querySelectorAll('select[name="runa_secundaria[]"]').forEach(function(sel) {
                            fd.append('runa_secundaria[]', sel.value);
                        });
                    }
                }
            } else if (entidad === 'equipo') {
                fd.append('nombre',      document.getElementById('edit_nombre').value);
                fd.append('id_liga',     document.getElementById('edit_id_liga').value);
                fd.append('logo',        document.getElementById('edit_logo').value);
                fd.append('descripcion', document.getElementById('edit_descripcion').value);
                fd.append('ranking',     document.getElementById('edit_ranking').value || 0);
                fd.append('video',       document.getElementById('edit_video').value);
                ['Top','Jungla','Mid','ADC','Support','Coach'].forEach(function(pos) {
                    ['id','nombre','nick','foto_url'].forEach(function(campo) {
                        const inp = document.querySelector('[name="roster_' + campo + '_' + pos + '"]');
                        fd.append('roster_' + campo + '_' + pos, inp ? inp.value : '');
                    });
                });
            } else if (entidad === 'partido') {
                fd.append('fecha',       document.getElementById('edit_fecha').value);
                fd.append('id_equipo1',  document.getElementById('edit_eq1').value);
                fd.append('id_equipo2',  document.getElementById('edit_eq2').value);
                fd.append('res1',        document.getElementById('edit_res1').value || 0);
                fd.append('res2',        document.getElementById('edit_res2').value || 0);
                fd.append('stream_url',  document.getElementById('edit_stream_url').value.trim());
            }

            try {
                const res  = await fetch('/CONTROLADORES/api_admin.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
                    const alertaMap = { campeon: 'alerta-campeones', equipo: 'alerta-equipos', partido: 'alerta-partidos' };
                    accionExitosa(alertaMap[entidad] || 'alerta-modal', 'Cambios guardados correctamente.');
                } else {
                    accionError('alerta-modal', data.error || 'Error al guardar.');
                }
            } catch(err) {
                accionError('alerta-modal', 'Error de conexión.');
            }
        });

        // ─── INIT: activar tab por hash + bloque de runas del ADD form ────
        // CRÍTICO: las dos tareas están en try/catch independientes — si la activación
        // de tab por hash falla (p. ej. bootstrap aún no cargado), el bloque de runas
        // del formulario ADD SIGUE inicializándose. Antes esto petaba en cascada y dejaba
        // el formulario sin <select> de runas → el usuario no podía elegir → POST sin runas.
        (function() {
            try {
                const hashToTab = {
                    'seccion-resumen':       'resumen-tab',
                    'seccion-usuarios':      'usuarios-tab',
                    'seccion-publicaciones': 'publicaciones-tab',
                    'seccion-campeones':     'campeones-tab',
                    'seccion-equipos':       'equipos-tab',
                    'seccion-partidos':      'partidos-tab'
                };
                const hash  = window.location.hash.replace('#', '');
                const tabId = hashToTab[hash];
                if (tabId && window.bootstrap && bootstrap.Tab) {
                    const tabEl = document.getElementById(tabId);
                    if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
                }
            } catch (err) {
                console.error('[gestion_admin] Error activando tab por hash:', err);
            }

            try {
                // Inicializar bloque dinámico de runas en el formulario ADD
                const addCont = document.getElementById('add-runas-container');
                if (addCont) {
                    addCont.innerHTML = generarRunasBlockHtml('add', 8000, 8100);
                    const inner = addCont.querySelector('.runa-context');
                    if (inner) inicializarRunasBlock(inner, null);
                }
            } catch (err) {
                console.error('[gestion_admin] Error inicializando bloque de runas ADD:', err);
            }
        })();
    </script>
</body>
</html>
