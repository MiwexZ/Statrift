<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';
require_once __DIR__ . '/../MODELOS/modelo_equipo.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: /VISTAS/equipos.php"); exit; }

$modelo = new modelo_equipo();
$equipo = $modelo->get_equipo_info($id);
if (!$equipo) { header("Location: /VISTAS/equipos.php"); exit; }

$roster = $modelo->get_roster($id);

$nombre      = htmlspecialchars($equipo['nombre']      ?? '');
$logo        = htmlspecialchars($equipo['Logo']        ?? '');
$descripcion = htmlspecialchars($equipo['descripcion'] ?? '');
$liga        = htmlspecialchars($equipo['nombre_liga'] ?? '');
$ranking     = intval($equipo['ranking']);
$video       = htmlspecialchars($equipo['video_highlights'] ?? '');

$pos_meta = [
    'Top'     => ['icon' => 'fa-shield-halved',      'color' => '#3b82f6', 'label' => 'Top'],
    'Jungla'  => ['icon' => 'fa-tree',               'color' => '#22c55e', 'label' => 'Jungla'],
    'Mid'     => ['icon' => 'fa-star',               'color' => '#a855f7', 'label' => 'Mid'],
    'ADC'     => ['icon' => 'fa-crosshairs',         'color' => '#ef4444', 'label' => 'ADC'],
    'Support' => ['icon' => 'fa-hand-holding-heart', 'color' => '#e5b962', 'label' => 'Support'],
    'Coach'   => ['icon' => 'fa-clipboard',          'color' => '#94a3b8', 'label' => 'Coach'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nombre ?> — StatRift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=2.1">
    <style>
        .team-hero-logo {
            max-width: 180px;
            max-height: 180px;
            object-fit: contain;
            filter: drop-shadow(0 8px 32px rgba(197,160,89,0.4));
        }
        .roster-card {
            background: rgba(15,23,42,0.7);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all .25s ease;
        }
        .roster-card:hover {
            border-color: rgba(197,160,89,0.4);
            transform: translateX(4px);
            background: rgba(197,160,89,0.06);
        }
        .roster-pos-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .roster-text {
            flex: 1 1 auto;
            min-width: 0;          /* permite que el texto se trunque sin empujar el badge */
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .roster-nick {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gold-accent);
            line-height: 1.2;
        }
        .roster-name {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .roster-pos-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-left: auto;
            padding: .2rem .7rem;
            border-radius: 20px;
            background: rgba(255,255,255,0.06);
            flex-shrink: 0;        /* el badge nunca se encoge */
            white-space: nowrap;
        }
        .team-hero-title { font-size: 2.5rem; }
        @media (max-width: 575.98px) {
            .team-hero-title { font-size: 1.7rem; }
            .team-hero-logo { max-width: 130px; max-height: 130px; }
            .roster-card { padding: .75rem .9rem; gap: .65rem; }
            .roster-nick { font-size: 1rem; }
        }
</style>
</head>
<body>
<?php generar_header(); ?>

<main class="container py-5 mt-3" style="max-width:960px">

    <!-- BREADCRUMB -->
    <nav class="mb-4" data-aos="fade-right">
        <a href="/VISTAS/equipos.php" class="text-muted small text-decoration-none">
            <i class="fa-solid fa-shield-halved me-1"></i>Equipos
        </a>
        <span class="text-muted small mx-2">/</span>
        <span class="text-gold small"><?= $nombre ?></span>
    </nav>

    <!-- HERO DEL EQUIPO -->
    <div class="bg-glass p-4 p-md-5 rounded-4 mb-4 text-center" data-aos="fade-up">
        <?php if ($logo): ?>
            <img src="<?= $logo ?>" alt="<?= $nombre ?>" class="team-hero-logo mb-3"
                 onerror="this.style.display='none'">
        <?php endif; ?>

        <h1 class="text-gold fw-bold mb-2 team-hero-title"><?= $nombre ?></h1>

        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap mb-3">
            <?php if ($liga): ?>
                <span class="badge bg-dark border border-secondary text-gold px-3 py-2">
                    <i class="fa-solid fa-trophy me-1"></i><?= $liga ?>
                </span>
            <?php endif; ?>
            <?php if ($ranking > 0): ?>
                <span class="team-rank-badge">GPR #<strong><?= $ranking ?></strong></span>
            <?php endif; ?>
        </div>

        <?php if ($descripcion): ?>
            <p class="text-muted mx-auto" style="max-width:680px;line-height:1.8"><?= $descripcion ?></p>
        <?php endif; ?>
    </div>

    <!-- ROSTER — ancho completo -->
    <div class="bg-glass p-4 rounded-4 mb-4" data-aos="fade-up">
        <h4 class="text-gold mb-4">
            <i class="fa-solid fa-users me-2"></i>Roster Oficial
        </h4>
        <?php if (empty($roster)): ?>
            <p class="text-muted text-center py-4">
                <i class="fa-solid fa-user-slash d-block fs-2 mb-2"></i>
                Sin roster registrado.
            </p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($roster as $jugador):
                    $pos   = $jugador['posicion'] ?? 'Coach';
                    $meta  = $pos_meta[$pos] ?? $pos_meta['Coach'];
                    $nick  = htmlspecialchars($jugador['nick']   ?? '');
                    $nreal = htmlspecialchars($jugador['nombre'] ?? '');
                ?>
                <div class="col-12 col-md-6">
                    <div class="roster-card">
                        <div class="roster-pos-icon">
                            <i class="fa-solid <?= $meta['icon'] ?>" style="color:<?= $meta['color'] ?>"></i>
                        </div>
                        <div class="roster-text">
                            <div class="roster-nick"><?= $nick ?></div>
                            <?php if ($nreal): ?>
                                <div class="roster-name"><?= $nreal ?></div>
                            <?php endif; ?>
                        </div>
                        <span class="roster-pos-label" style="color:<?= $meta['color'] ?>;border:1px solid <?= $meta['color'] ?>33">
                            <?= $meta['label'] ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($video): ?>
    <!-- HIGHLIGHTS — solo si hay vídeo -->
    <div class="bg-glass p-4 rounded-4 mb-4" data-aos="fade-up" data-aos-delay="100">
        <h4 class="text-gold mb-4">
            <i class="fa-solid fa-film me-2"></i>Highlights
        </h4>
        <div class="video-wrapper">
            <iframe
                src="<?= $video ?>"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen>
            </iframe>
        </div>
    </div>
    <?php endif; ?>

    <!-- BOTÓN VOLVER -->
    <div class="text-center mt-5" data-aos="fade-up">
        <a href="/VISTAS/equipos.php" class="btn btn-outline-light px-4">
            <i class="fa-solid fa-arrow-left me-2"></i>Volver a Equipos
        </a>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once: true, offset: 40, duration: 700 });</script>
</body>
</html>
