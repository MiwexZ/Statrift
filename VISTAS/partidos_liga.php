<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';
require_once __DIR__ . '/../MODELOS/modelo_ligas.php';
require_once __DIR__ . '/../MODELOS/modelo_partido.php';

$id_liga = intval($_GET['id'] ?? 0);
if ($id_liga <= 0) { header("Location: /VISTAS/ligas.php"); exit; }

$modelo_liga    = new modelo_liga();
$modelo_partido = new modelo_partido();

$liga    = $modelo_liga->get_liga_info($id_liga);
if (!$liga) { header("Location: /VISTAS/ligas.php"); exit; }

$partidos = $modelo_partido->get_partidos_por_liga($id_liga);

$nombre_liga = htmlspecialchars($liga['nombre'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nombre_liga ?> — Partidos | StatRift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=1.7">
    <style>
        .match-row {
            background: rgba(15,23,42,0.6);
            border: 1px solid rgba(197,160,89,0.12);
            border-radius: 12px;
            transition: border-color .25s, transform .2s;
        }
        .match-row:hover {
            border-color: rgba(197,160,89,0.45);
            transform: translateY(-2px);
        }
        .team-logo {
            width: 44px; height: 44px;
            object-fit: contain; border-radius: 6px;
        }
        .vs-badge {
            font-size: .75rem; font-weight: 800;
            color: #ef4444; letter-spacing: 1px;
        }
        .match-date-pill {
            font-size: .72rem; font-weight: 600;
            background: rgba(197,160,89,0.12);
            color: var(--gold-accent);
            border-radius: 20px;
            padding: .2rem .75rem;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<?php generar_header(); ?>

<section class="page-hero text-center" data-aos="fade-down" data-aos-duration="800">
    <div class="container">
        <div class="page-hero-icon"><i class="fa-solid fa-calendar-days"></i></div>
        <h1 class="page-hero-title"><?= $nombre_liga ?></h1>
        <p class="page-hero-sub">Próximos partidos — los dos meses siguientes</p>
    </div>
</section>

<main class="container my-5" style="max-width: 780px;">

    <div class="mb-4">
        <a href="/VISTAS/ligas.php" class="btn btn-outline-light btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i>Volver a Ligas
        </a>
    </div>

    <?php if (empty($partidos)): ?>
    <div class="bg-glass text-center p-5 rounded-4" data-aos="fade-up">
        <i class="fa-solid fa-calendar-xmark fs-1 text-gold d-block mb-3" style="opacity:.6"></i>
        <p class="text-light mb-0" style="opacity:.8">No hay partidos programados para los próximos dos meses.</p>
    </div>
    <?php else: ?>
    <div class="d-flex flex-column gap-3">
        <?php foreach ($partidos as $i => $p):
            $fecha_fmt  = date('d M Y', strtotime($p['fecha']));
            $equipo1    = htmlspecialchars($p['equipo1']);
            $equipo2    = htmlspecialchars($p['equipo2']);
            $logo1      = htmlspecialchars($p['logo1'] ?? '');
            $logo2      = htmlspecialchars($p['logo2'] ?? '');
            $id_partido = intval($p['id']);
            $delay      = min($i * 60, 400);
        ?>
        <a href="/VISTAS/ver_partido.php?id=<?= $id_partido ?>" class="text-decoration-none"
           data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <div class="match-row d-flex align-items-center justify-content-between px-4 py-3 gap-3">

                <!-- Fecha -->
                <span class="match-date-pill flex-shrink-0">
                    <i class="fa-regular fa-calendar me-1"></i><?= $fecha_fmt ?>
                </span>

                <!-- Equipo 1 -->
                <div class="d-flex align-items-center gap-2 justify-content-end flex-grow-1">
                    <span class="fw-bold text-light text-end"><?= $equipo1 ?></span>
                    <?php if ($logo1): ?>
                        <img src="<?= $logo1 ?>" alt="<?= $equipo1 ?>" class="team-logo"
                             onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>

                <!-- VS -->
                <span class="vs-badge flex-shrink-0">VS</span>

                <!-- Equipo 2 -->
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <?php if ($logo2): ?>
                        <img src="<?= $logo2 ?>" alt="<?= $equipo2 ?>" class="team-logo"
                             onerror="this.style.display='none'">
                    <?php endif; ?>
                    <span class="fw-bold text-light"><?= $equipo2 ?></span>
                </div>

                <!-- Flecha -->
                <i class="fa-solid fa-chevron-right text-muted flex-shrink-0"></i>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once: true, offset: 40, duration: 700 });</script>
</body>
</html>
