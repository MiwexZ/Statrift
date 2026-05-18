<?php
ob_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';
require_once __DIR__ . '/../PHP/DDragonService.php';
require_once __DIR__ . '/../MODELOS/modelo_build.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: /VISTAS/campeones.php"); exit; }

$modelo  = new modelo_build();
$campeon = $modelo->get_campeon($id);
if (!$campeon) { header("Location: /VISTAS/campeones.php"); exit; }

$build = $modelo->get_build($id);

// ── Cargar items por fase (con fallback si las columnas nuevas aún no existen) ──
$items_by_fase = ['starter' => [], 'early' => [], 'core' => [], 'full' => []];
$ddragon_ready = false; // flag para saber si el SQL de actualización se ejecutó

if ($build) {
    $db = conectar::conexion();

    // Detectar si existe la columna riot_id en objeto
    $col_check = $db->query("SHOW COLUMNS FROM objeto LIKE 'riot_id'");
    $has_riot_id = ($col_check && $col_check->num_rows > 0);

    $col_check2 = $db->query("SHOW COLUMNS FROM build_objeto LIKE 'fase'");
    $has_fase   = ($col_check2 && $col_check2->num_rows > 0);

    $ddragon_ready = $has_riot_id; // usamos esto para saber si DDragon está disponible

    if ($has_riot_id && $has_fase) {
        $stmt = $db->prepare(
            "SELECT o.riot_id, o.nombre, bo.fase FROM build_objeto bo
             JOIN objeto o ON bo.id_objeto = o.id
             WHERE bo.id_build = ? ORDER BY bo.orden ASC"
        );
    } else {
        $stmt = $db->prepare(
            "SELECT o.nombre, NULL as riot_id, 'core' as fase FROM build_objeto bo
             JOIN objeto o ON bo.id_objeto = o.id
             WHERE bo.id_build = ? ORDER BY bo.orden ASC"
        );
    }

    if ($stmt) {
        $stmt->bind_param("i", $build['id']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $fase = $row['fase'] ?? 'core';
                $items_by_fase[$fase][] = $row;
            }
        }
    }
}

// ── Cargar runas seleccionadas ──
$selected_rune_ids = [];
if ($build && $ddragon_ready) {
    $db = conectar::conexion();
    $col_check3 = $db->query("SHOW COLUMNS FROM runa LIKE 'riot_id'");
    if ($col_check3 && $col_check3->num_rows > 0) {
        $stmt2 = $db->prepare(
            "SELECT r.riot_id FROM build_runa br JOIN runa r ON br.id_runa = r.id WHERE br.id_build = ?"
        );
        if ($stmt2) {
            $stmt2->bind_param("i", $build['id']);
            $stmt2->execute();
            $res2 = $stmt2->get_result();
            if ($res2) {
                while ($r = $res2->fetch_assoc()) {
                    if ($r['riot_id']) $selected_rune_ids[] = (int)$r['riot_id'];
                }
            }
        }
    }
}

// ── Árboles de runas desde DDragon (solo si el SQL está ejecutado) ──
$primary_path   = null;
$secondary_path = null;
$col_bp = conectar::conexion()->query("SHOW COLUMNS FROM build LIKE 'primary_path'");
if ($build && $col_bp && $col_bp->num_rows > 0) {
    $primary_path   = DDragonService::getRunePath((int)($build['primary_path']   ?? 8000));
    $secondary_path = DDragonService::getRunePath((int)($build['secondary_path'] ?? 8100));
}

$nombre     = htmlspecialchars($campeon['nombre']);
$foto       = htmlspecialchars($campeon['foto'] ?? '');
$rol        = htmlspecialchars($campeon['rol'] ?? '');
$descripcion = htmlspecialchars($campeon['descripcion'] ?? '');
$habilidades = [
    'Q' => htmlspecialchars($campeon['q'] ?? '—'),
    'W' => htmlspecialchars($campeon['w'] ?? '—'),
    'E' => htmlspecialchars($campeon['e'] ?? '—'),
    'R' => htmlspecialchars($campeon['r'] ?? '—'),
];
$skill_colors = ['Q' => '#3b82f6', 'W' => '#22c55e', 'E' => '#a855f7', 'R' => '#ef4444'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nombre ?> Build - StatRift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=2.1">
    <style>
        /* ─── HERO ─────────────────────────────────────────── */
        .build-hero {
            position: relative; height: 380px; overflow: hidden;
            border-radius: 0 0 24px 24px; margin-bottom: 2rem;
        }
        .build-hero img {
            width:100%; height:100%; object-fit:cover;
            object-position: top center; filter: brightness(0.4);
        }
        .build-hero-overlay {
            position:absolute; bottom:0; left:0; right:0;
            padding: 2rem 3rem;
            background: linear-gradient(transparent, rgba(15,23,42,0.97));
        }
        .build-hero-overlay h1 { font-size:2.8rem; color:var(--gold-accent); }
        .role-badge {
            display:inline-block;
            background: rgba(197,160,89,.2); border:1px solid var(--gold-accent);
            color:var(--gold-accent); padding:.3rem 1rem; border-radius:50px;
            font-size:.82rem; font-weight:600; letter-spacing:.5px;
        }

        /* ─── PANEL GLASSMORPHISM ───────────────────────────── */
        .panel {
            background: rgba(15,23,42,0.75);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(197,160,89,0.18);
            border-radius: 14px;
            padding: 1.5rem;
        }
        .panel-title {
            font-size:.7rem; font-weight:700; letter-spacing:2px;
            text-transform:uppercase; color:var(--gold-accent);
            border-bottom:1px solid rgba(197,160,89,0.2);
            padding-bottom:.6rem; margin-bottom:1.2rem;
        }

        /* ─── WIN RATE BAR ───────────────────────────────────── */
        .wr-bar-wrap { height:6px; background:rgba(255,255,255,.1); border-radius:3px; }
        .wr-bar { height:100%; border-radius:3px;
            background: linear-gradient(90deg, var(--gold-accent), #e5b962); }

        /* ─── RUNES TREE ─────────────────────────────────────── */
        .rune-path-header { display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem; }
        .rune-path-icon { width:40px; height:40px; }
        .rune-path-name { font-weight:700; font-size:1.05rem; color:#fff; }

        .rune-slot { display:flex; flex-wrap:wrap; justify-content:center; gap:.75rem; margin-bottom:.85rem; }
        .rune-bubble {
            width: 54px; height: 54px; border-radius:50%;
            background: rgba(15,23,42,.8);
            border: 2px solid rgba(255,255,255,0.12);
            display:flex; align-items:center; justify-content:center;
            transition: all .25s ease; cursor:default; flex-shrink:0;
        }
        .rune-bubble img { width:40px; height:40px; border-radius:50%; filter:grayscale(1) brightness(.45); transition:.25s; }
        .rune-bubble.active { border-color: var(--gold-accent); box-shadow: 0 0 18px rgba(197,160,89,.55); }
        .rune-bubble.active img { filter: none; }
        .rune-bubble.keystone { width:70px; height:70px; }
        .rune-bubble.keystone img { width:56px; height:56px; }

        .secondary-slot { display:flex; flex-wrap:wrap; justify-content:center; gap:.6rem; margin-bottom:.7rem; }
        .rune-bubble-sm {
            width:46px; height:46px; border-radius:50%;
            background:rgba(15,23,42,.8); border:2px solid rgba(255,255,255,.12);
            display:flex; align-items:center; justify-content:center; transition:.25s;
            flex-shrink:0;
        }
        .rune-bubble-sm img { width:34px; height:34px; border-radius:50%; filter:grayscale(1) brightness(.45); transition:.25s; }
        .rune-bubble-sm.active { border-color:var(--gold-accent); box-shadow:0 0 12px rgba(197,160,89,.45); }
        .rune-bubble-sm.active img { filter:none; }
        .panel-runes { min-height: 420px; }

        /* ─── ITEMS ────────────────────────────────────────── */
        .item-phase-label { font-size:.65rem; font-weight:700; letter-spacing:1.5px;
            text-transform:uppercase; color:var(--text-muted); margin-bottom:.4rem; }
        .item-phase-time { font-size:.7rem; color:var(--gold-accent); }
        .item-strip { display:flex; align-items:center; gap:.4rem; flex-wrap:wrap; margin-bottom:1rem; }
        .item-icon {
            width:46px; height:46px; border-radius:6px;
            border:2px solid rgba(197,160,89,.25);
            transition: transform .2s, border-color .2s, box-shadow .2s;
            background:#0f172a;
        }
        .item-icon:hover { transform:scale(1.15); border-color:var(--gold-accent);
            box-shadow:0 0 16px rgba(197,160,89,.45); }

        /* ─── SKILLS ─────────────────────────────────────────── */
        .skill-card {
            background:rgba(15,23,42,.6);
            border:1px solid rgba(255,255,255,.07);
            border-radius:10px; padding:1rem;
            transition:all .3s ease; height:100%;
        }
        .skill-card:hover { border-color:rgba(197,160,89,.35); transform:translateY(-3px); }
        .skill-key-big {
            width:38px; height:38px; border-radius:7px;
            display:flex; align-items:center; justify-content:center;
            font-weight:800; font-size:1rem; color:#fff; flex-shrink:0;
        }
        .stats-label {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #94a3b8; /* Un gris más claro para mejor contraste */
            opacity: 0.8;
        }

        /* ─── ITEMS COLUMN (responsive) ──────────────────────── */
        .items-col { flex: 0 0 auto; width: 280px; }

        @media (max-width: 991.98px) {
            .build-hero { height: 280px; }
            .build-hero-overlay { padding: 1.25rem 1.25rem; }
            .build-hero-overlay h1 { font-size: 1.9rem; }
            .items-col { width: 100%; flex: 1 1 auto; }
            .panel-runes { min-height: 0; }
        }
        @media (max-width: 575.98px) {
            .build-hero { height: 220px; border-radius: 0 0 16px 16px; }
            .build-hero-overlay h1 { font-size: 1.55rem; }
            .panel { padding: 1rem; }
            .rune-bubble { width: 44px; height: 44px; }
            .rune-bubble img { width: 32px; height: 32px; }
            .rune-bubble.keystone { width: 58px; height: 58px; }
            .rune-bubble.keystone img { width: 46px; height: 46px; }
            .rune-bubble-sm { width: 38px; height: 38px; }
            .rune-bubble-sm img { width: 28px; height: 28px; }
            .skill-key-big { width: 32px; height: 32px; font-size: .85rem; }
        }
    </style>
</head>
<body>
    <?php generar_header(); ?>

    <!-- HERO BANNER -->
    <div class="build-hero">
        <img src="<?= $foto ?>" alt="<?= $nombre ?>">
        <div class="build-hero-overlay">
            <div class="d-flex align-items-center gap-3 mb-2">
                <span class="role-badge"><i class="fa-solid fa-crosshairs me-1"></i><?= $rol ?></span>
                <?php if ($build): ?>
                    <span class="text-muted small">Parche <?= DDragonService::getVersion() ?></span>
                <?php endif; ?>
            </div>
            <h1 class="mb-0"><?= $nombre ?></h1>
        </div>
    </div>

    <div class="container pb-5">

        <!-- STATS BAR -->
        <?php if ($build): ?>
        <div class="panel mb-4 d-flex flex-wrap gap-4 align-items-center" data-aos="fade-up">
            <div>
                <div class="stats-label mb-1">BUILD MÁS POPULAR</div>
                <div class="fw-bold text-gold fs-5"><?= htmlspecialchars($build['nombre_build']) ?></div>
            </div>
            <div>
                <div class="stats-label mb-1">Win Rate</div>
                <div class="fw-bold text-light fs-5"><?= $build['win_rate'] ?>%</div>
                <div class="wr-bar-wrap mt-1" style="width:120px">
                    <div class="wr-bar" style="width:<?= $build['win_rate'] ?>%"></div>
                </div>
            </div>
            <div>
                <div class="stats-label mb-1">Partidas</div>
                <div class="fw-bold text-light"><?= number_format($build['total_matches'] ?? 0) ?></div>
            </div>
            <div>
                <div class="stats-label mb-1">Pick Rate</div>
                <div class="fw-bold text-gold"><?= $build['popularidad'] ?>%</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- DESCRIPCIÓN + HABILIDADES -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="panel h-100">
                    <div class="panel-title"><i class="fa-solid fa-scroll me-2"></i>Análisis Técnico</div>
                    <p class="text-light mb-0" style="line-height:1.85;"><?= $descripcion ?></p>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="panel h-100">
                    <div class="panel-title"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Habilidades</div>
                    <div class="row g-2">
                        <?php foreach ($habilidades as $key => $desc): ?>
                        <div class="col-12 col-sm-6">
                            <div class="skill-card">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="skill-key-big" style="background:<?= $skill_colors[$key] ?>"><?= $key ?></div>
                                    <small class="text-light"><?= $desc ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- RUNAS + ITEMS (OP.GG STYLE) -->
        <?php if ($build): ?>
        <div class="d-flex flex-column flex-lg-row gap-4 align-items-stretch" data-aos="fade-up" data-aos-delay="100">

            <!-- ── RUNAS ── -->
            <div style="flex: 1 1 auto; min-width: 0;">
                <div class="panel panel-runes h-100">
                    <div class="panel-title"><i class="fa-solid fa-gem me-2"></i>Runas</div>
                    <div class="row g-3">
                        <!-- PRIMARY PATH -->
                        <div class="col-12 col-sm-6">
                            <?php if ($primary_path): ?>
                            <div class="rune-path-header">
                                <img src="<?= DDragonService::runeIcon($primary_path['icon']) ?>" class="rune-path-icon" alt="">
                                <span class="rune-path-name"><?= htmlspecialchars($primary_path['name']) ?></span>
                            </div>
                            <?php foreach ($primary_path['slots'] as $si => $slot): ?>
                            <div class="rune-slot">
                                <?php foreach ($slot['runes'] as $rune): ?>
                                <?php $active = in_array((int)$rune['id'], $selected_rune_ids); ?>
                                <div class="rune-bubble <?= $si===0 ? 'keystone' : '' ?> <?= $active ? 'active' : '' ?>"
                                     title="<?= htmlspecialchars($rune['name']) ?>">
                                    <img src="<?= DDragonService::runeIcon($rune['icon']) ?>" alt="<?= htmlspecialchars($rune['name']) ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <!-- SECONDARY PATH -->
                        <div class="col-12 col-sm-6">
                            <?php if ($secondary_path): ?>
                            <div class="rune-path-header">
                                <img src="<?= DDragonService::runeIcon($secondary_path['icon']) ?>" class="rune-path-icon" alt="">
                                <span class="rune-path-name" style="font-size:.8rem"><?= htmlspecialchars($secondary_path['name']) ?></span>
                            </div>
                            <?php foreach (array_slice($secondary_path['slots'], 1) as $slot): ?>
                            <div class="secondary-slot">
                                <?php foreach ($slot['runes'] as $rune): ?>
                                <?php $active = in_array((int)$rune['id'], $selected_rune_ids); ?>
                                <div class="rune-bubble-sm <?= $active ? 'active' : '' ?>"
                                     title="<?= htmlspecialchars($rune['name']) ?>">
                                    <img src="<?= DDragonService::runeIcon($rune['icon']) ?>" alt="<?= htmlspecialchars($rune['name']) ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── ITEMS POR FASE ── -->
            <div class="items-col">
                <div class="panel h-100">
                    <div class="panel-title"><i class="fa-solid fa-hammer me-2"></i>Items</div>

                    <?php
                    $fases = [
                        'starter' => ['label' => 'Starter Items',  'time' => '@ Inicio'],
                        'early'   => ['label' => 'Early Items',    'time' => '@ 5 min'],
                        'core'    => ['label' => 'Core Items',     'time' => '@ 20 min'],
                        'full'    => ['label' => 'Full Build',     'time' => '@ 35 min'],
                    ];
                    foreach ($fases as $fase_key => $fase_meta):
                        if (empty($items_by_fase[$fase_key])) continue;
                    ?>
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="item-phase-label"><?= $fase_meta['label'] ?></span>
                            <span class="item-phase-time"><?= $fase_meta['time'] ?></span>
                        </div>
                        <div class="item-strip">
                            <?php foreach ($items_by_fase[$fase_key] as $item):
                                $riot_id = $item['riot_id'] ?? '';
                                $icon_url = $riot_id ? DDragonService::itemIcon($riot_id) : '';
                                $iname    = htmlspecialchars($item['nombre']);
                            ?>
                            <?php if ($icon_url): ?>
                                <img src="<?= $icon_url ?>" alt="<?= $iname ?>" class="item-icon" title="<?= $iname ?>">
                            <?php else: ?>
                                <div class="item-icon d-flex align-items-center justify-content-center text-muted" style="font-size:.6rem"><?= $iname ?></div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
        <?php else: ?>
        <div class="panel text-center text-muted py-5">
            <i class="fa-solid fa-hammer fs-1 mb-3 d-block"></i>
            <p>No hay build registrada para este campeón. ¡Pídele al admin que la añada!</p>
        </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="/VISTAS/campeones.php" class="btn btn-outline-light px-4 me-2">
                <i class="fa-solid fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ once: true });</script>
</body>
</html>
