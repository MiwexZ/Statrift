<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';
require_once __DIR__ . '/../MODELOS/modelo_partido.php';
require_once __DIR__ . '/../MODELOS/modelo_ligas.php';

$id_partido = intval($_GET['id'] ?? 0);
if ($id_partido <= 0) { header("Location: /VISTAS/ligas.php"); exit; }

$modelo_partido = new modelo_partido();
$partido = $modelo_partido->get_partido_detalle($id_partido);
if (!$partido) { header("Location: /VISTAS/ligas.php"); exit; }

$modelo_liga = new modelo_liga();
$liga = $partido['id_liga'] ? $modelo_liga->get_liga_info((int)$partido['id_liga']) : null;

$fecha_partido  = $partido['fecha'];
$hoy            = date('Y-m-d');
$es_hoy         = ($fecha_partido === $hoy);
$ya_paso        = ($fecha_partido < $hoy);
$desbloqueado   = ($fecha_partido <= $hoy); // hoy o pasado

$equipo1  = htmlspecialchars($partido['equipo1']);
$equipo2  = htmlspecialchars($partido['equipo2']);
$logo1    = htmlspecialchars($partido['logo1'] ?? '');
$logo2    = htmlspecialchars($partido['logo2'] ?? '');
$fecha_fmt = date('d \d\e F \d\e Y', strtotime($fecha_partido));
$res1     = $partido['res1'] ?? null;
$res2     = $partido['res2'] ?? null;

$titulo   = "{$equipo1} vs {$equipo2}";
$liga_nombre = htmlspecialchars($liga['nombre'] ?? 'Liga');
$id_liga  = intval($partido['id_liga'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> — StatRift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=1.1">
    <style>
        .match-hero {
            background: rgba(15,23,42,0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(197,160,89,0.18);
            border-radius: 18px;
            padding: 2.5rem;
        }
        .team-block { text-align: center; flex: 1; }
        .team-logo-lg {
            width: 90px; height: 90px;
            object-fit: contain; border-radius: 12px;
            margin-bottom: .75rem;
        }
        .team-name-lg { font-size: 1.4rem; font-weight: 700; color: #fff; }
        .vs-central {
            font-size: 2rem; font-weight: 900;
            color: #ef4444; letter-spacing: 3px;
            flex-shrink: 0; padding: 0 1.5rem;
        }
        .score-box {
            font-size: 2.5rem; font-weight: 900;
            color: var(--gold-accent); line-height: 1;
        }

        /* Reproductor */
        .player-wrap {
            position: relative;
            width: 100%; padding-top: 56.25%; /* 16:9 */
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(197,160,89,0.2);
        }
        .player-wrap iframe {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
            border: none;
        }
        .player-locked {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(10,12,22,0.92);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 1rem; z-index: 2;
        }
        .lock-icon {
            font-size: 3.5rem; color: var(--gold-accent);
            opacity: .7;
        }
        .lock-msg {
            font-size: 1.05rem; color: #e2e8f0;
            text-align: center; max-width: 360px;
            line-height: 1.6;
        }
        .lock-date {
            font-size: .85rem; color: var(--gold-accent);
            font-weight: 600;
        }
        .badge-estado {
            font-size: .78rem; padding: .4rem 1rem;
            border-radius: 20px; font-weight: 600;
        }
    </style>
</head>
<body>
<?php generar_header(); ?>

<main class="container my-5" style="max-width: 860px;">

    <!-- Breadcrumb -->
    <nav class="mb-4" data-aos="fade-right">
        <a href="/VISTAS/ligas.php" class="text-muted small text-decoration-none">
            <i class="fa-solid fa-trophy me-1"></i>Ligas
        </a>
        <?php if ($id_liga > 0): ?>
        <span class="text-muted small mx-2">/</span>
        <a href="/VISTAS/partidos_liga.php?id=<?= $id_liga ?>" class="text-muted small text-decoration-none">
            <?= $liga_nombre ?>
        </a>
        <?php endif; ?>
        <span class="text-muted small mx-2">/</span>
        <span class="text-gold small"><?= $titulo ?></span>
    </nav>

    <!-- Cabecera del partido -->
    <div class="match-hero mb-4" data-aos="fade-up">

        <!-- Estado -->
        <div class="text-center mb-3">
            <?php if ($es_hoy): ?>
                <span class="badge-estado bg-danger text-white">
                    <i class="fa-solid fa-circle me-1" style="animation:pulse 1s infinite"></i>EN DIRECTO HOY
                </span>
            <?php elseif ($ya_paso): ?>
                <span class="badge-estado bg-secondary text-white">
                    <i class="fa-solid fa-flag-checkered me-1"></i>Partido finalizado
                </span>
            <?php else: ?>
                <span class="badge-estado text-gold" style="background:rgba(197,160,89,0.15);border:1px solid rgba(197,160,89,0.35)">
                    <i class="fa-regular fa-clock me-1"></i>Próximamente · <?= $fecha_fmt ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Equipos -->
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">

            <!-- Equipo 1 -->
            <div class="team-block">
                <?php if ($logo1): ?>
                    <img src="<?= $logo1 ?>" alt="<?= $equipo1 ?>" class="team-logo-lg d-block mx-auto"
                         onerror="this.style.display='none'">
                <?php else: ?>
                    <div class="team-logo-lg d-flex align-items-center justify-content-center mx-auto bg-dark rounded-3">
                        <i class="fa-solid fa-shield-halved text-gold fs-2"></i>
                    </div>
                <?php endif; ?>
                <div class="team-name-lg"><?= $equipo1 ?></div>
                <?php if ($ya_paso && $res1 !== null): ?>
                    <div class="score-box mt-2"><?= intval($res1) ?></div>
                <?php endif; ?>
            </div>

            <!-- VS / Marcador -->
            <div class="vs-central">
                <?php if ($ya_paso && $res1 !== null && $res2 !== null): ?>
                    <span class="text-muted" style="font-size:1.5rem">
                        <?= intval($res1) ?> — <?= intval($res2) ?>
                    </span>
                <?php else: ?>
                    VS
                <?php endif; ?>
            </div>

            <!-- Equipo 2 -->
            <div class="team-block">
                <?php if ($logo2): ?>
                    <img src="<?= $logo2 ?>" alt="<?= $equipo2 ?>" class="team-logo-lg d-block mx-auto"
                         onerror="this.style.display='none'">
                <?php else: ?>
                    <div class="team-logo-lg d-flex align-items-center justify-content-center mx-auto bg-dark rounded-3">
                        <i class="fa-solid fa-shield-halved text-gold fs-2"></i>
                    </div>
                <?php endif; ?>
                <div class="team-name-lg"><?= $equipo2 ?></div>
                <?php if ($ya_paso && $res2 !== null): ?>
                    <div class="score-box mt-2"><?= intval($res2) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Fecha y liga -->
        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="fa-regular fa-calendar-alt me-1"></i><?= $fecha_fmt ?>
                <?php if ($liga): ?>
                    &nbsp;·&nbsp;
                    <i class="fa-solid fa-trophy me-1 text-gold"></i><?= $liga_nombre ?>
                <?php endif; ?>
            </small>
        </div>
    </div>

    <!-- Reproductor -->
    <div class="bg-glass rounded-4 p-4" data-aos="fade-up" data-aos-delay="100">
        <h5 class="text-gold mb-3">
            <i class="fa-solid fa-play-circle me-2"></i>Reproducción del partido
        </h5>

        <div class="player-wrap">
            <?php if ($desbloqueado): ?>
                <!-- Cuando llegue el día, aquí iría el iframe del stream real -->
                <!-- Por ejemplo: <iframe src="https://www.youtube.com/embed/VIDEO_ID" allowfullscreen></iframe> -->
                <div class="player-locked" style="background:rgba(10,12,22,0.75)">
                    <i class="fa-solid fa-film lock-icon" style="color:#94a3b8"></i>
                    <p class="lock-msg text-muted">
                        El vídeo de este partido estará disponible próximamente.<br>
                        <small>El administrador debe añadir el enlace de vídeo.</small>
                    </p>
                </div>
            <?php else: ?>
                <div class="player-locked">
                    <i class="fa-solid fa-lock lock-icon"></i>
                    <p class="lock-msg">La reproducción se activará el día del partido.</p>
                    <span class="lock-date">
                        <i class="fa-regular fa-calendar me-1"></i><?= $fecha_fmt ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botón volver -->
    <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="150">
        <?php if ($id_liga > 0): ?>
            <a href="/VISTAS/partidos_liga.php?id=<?= $id_liga ?>" class="btn btn-outline-light px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Volver a <?= $liga_nombre ?>
            </a>
        <?php else: ?>
            <a href="/VISTAS/ligas.php" class="btn btn-outline-light px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Volver a Ligas
            </a>
        <?php endif; ?>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once: true, offset: 40, duration: 700 });</script>
</body>
</html>
