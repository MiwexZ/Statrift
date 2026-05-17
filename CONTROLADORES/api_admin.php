<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../MODELOS/modelo_admin.php';

header('Content-Type: application/json');

if (!isset($_SESSION['nick']) || $_SESSION['nick'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado.']);
    exit;
}

$modelo = new modelo_admin();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// Consolida runa_primaria[] + runa_secundaria[] en runa_id[] (legado del modelo).
// Si ya viene runa_id[] (compatibilidad), se respeta.
function consolidar_runas_post(): array
{
    $primarias   = $_POST['runa_primaria']   ?? [];
    $secundarias = $_POST['runa_secundaria'] ?? [];
    if (empty($primarias) && empty($secundarias)) {
        return $_POST['runa_id'] ?? [];
    }
    $merged = array_merge((array)$primarias, (array)$secundarias);
    return array_values(array_filter(array_map('intval', $merged)));
}

if ($accion === 'delete_post') {
    $id = intval($_POST['id'] ?? 0);
    if ($modelo->delete_post($id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la publicación.']);
    }
    exit;
}

if ($accion === 'delete_user') {
    $id = intval($_POST['id'] ?? 0);
    if ($modelo->delete_user($id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el usuario. (No puedes eliminar al admin)']);
    }
    exit;
}

if ($accion === 'add_champion') {
    $nombre      = trim($_POST['nombre']      ?? '');
    $q           = trim($_POST['q']           ?? '');
    $w           = trim($_POST['w']           ?? '');
    $e           = trim($_POST['e']           ?? '');
    $r           = trim($_POST['r']           ?? '');
    $foto        = trim($_POST['foto']        ?? '');
    $rol         = trim($_POST['rol']         ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (!$nombre || !$foto) {
        echo json_encode(['success' => false, 'error' => 'Nombre y URL de foto son obligatorios.']);
        exit;
    }

    $id_campeon = $modelo->add_champion($nombre, $q, $w, $e, $r, $foto, $rol, $descripcion);
    if (!$id_campeon) {
        echo json_encode(['success' => false, 'error' => 'Error al añadir el campeón.']);
        exit;
    }

    // Build opcional
    $nombre_build = trim($_POST['nombre_build'] ?? '');
    if ($nombre_build) {
        $popularidad    = intval($_POST['popularidad']    ?? 80);
        $primary_path   = intval($_POST['primary_path']   ?? 8000);
        $secondary_path = intval($_POST['secondary_path'] ?? 8100);
        $win_rate       = floatval($_POST['win_rate']     ?? 50.0);
        $total_matches  = intval($_POST['total_matches']  ?? 10000);

        $id_build = $modelo->add_build($id_campeon, $nombre_build, $popularidad, $primary_path, $secondary_path, $win_rate, $total_matches);
        if (!$id_build) {
            echo json_encode(['success' => true, 'warning' => 'Campeón añadido, pero error al guardar la build.']);
            exit;
        }

        $obj_ids   = $_POST['obj_id']    ?? [];
        $obj_fases = $_POST['obj_fase']  ?? [];
        $obj_orden = $_POST['obj_orden'] ?? [];
        foreach ($obj_ids as $idx => $id_objeto) {
            $id_objeto = intval($id_objeto);
            if (!$id_objeto) continue;
            $fase  = in_array($obj_fases[$idx] ?? '', ['starter','early','core','full']) ? $obj_fases[$idx] : 'core';
            $orden = intval($obj_orden[$idx] ?? ($idx + 1));
            $modelo->add_build_objeto($id_build, $id_objeto, $orden, $fase);
        }

        $runa_ids = consolidar_runas_post();
        foreach ($runa_ids as $id_runa) {
            $id_runa = intval($id_runa);
            if (!$id_runa) continue;
            $modelo->add_build_runa($id_build, $id_runa);
        }
    }

    echo json_encode(['success' => true]);
    exit;
}

if ($accion === 'delete_champion') {
    $id = intval($_POST['id'] ?? 0);
    $result = $modelo->delete_champion($id);
    echo json_encode($result);
    exit;
}

if ($accion === 'add_partido') {
    $fecha      = trim($_POST['fecha']      ?? '');
    $id_equipo1 = intval($_POST['id_equipo1'] ?? 0);
    $id_equipo2 = intval($_POST['id_equipo2'] ?? 0);

    if (!$fecha || !$id_equipo1 || !$id_equipo2 || $id_equipo1 === $id_equipo2) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos o equipos iguales.']);
        exit;
    }

    if ($modelo->add_partido($fecha, $id_equipo1, $id_equipo2)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al crear el partido.']);
    }
    exit;
}

if ($accion === 'delete_partido') {
    $id = intval($_POST['id'] ?? 0);
    if ($modelo->delete_partido($id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el partido.']);
    }
    exit;
}

if ($accion === 'add_equipo') {
    $nombre     = trim($_POST['nombre']     ?? '');
    $id_liga    = intval($_POST['id_liga']  ?? 0);
    $logo       = trim($_POST['logo']       ?? '');
    $descripcion= trim($_POST['descripcion']?? '');
    $ranking    = intval($_POST['ranking']  ?? 0);
    $video      = trim($_POST['video']      ?? '');

    if (!$nombre) {
        echo json_encode(['success' => false, 'error' => 'El nombre del equipo es obligatorio.']);
        exit;
    }
    if (!$id_liga) {
        echo json_encode(['success' => false, 'error' => 'Debes seleccionar una liga.']);
        exit;
    }
    if ($logo && strpos($logo, 'http') !== 0) {
        echo json_encode(['success' => false, 'error' => 'La URL del logo debe comenzar por http.']);
        exit;
    }

    $id_equipo = $modelo->add_equipo($nombre, $id_liga, $logo, $descripcion, $ranking, $video);
    if (!$id_equipo) {
        echo json_encode(['success' => false, 'error' => 'Error al añadir el equipo.']);
        exit;
    }
    $roster_nombres = $_POST['roster_nombre'] ?? [];
    $roster_nicks   = $_POST['roster_nick']   ?? [];
    $roster_pos     = $_POST['roster_pos']    ?? [];
    foreach ($roster_nombres as $idx => $rn) {
        $rn  = trim($rn);
        $rk  = trim($roster_nicks[$idx] ?? '');
        $rp  = $roster_pos[$idx] ?? '';
        if ($rn && $rk && $rp) {
            $modelo->add_roster_member($id_equipo, $rn, $rk, $rp);
        }
    }
    echo json_encode(['success' => true]);
    exit;
}

if ($accion === 'delete_equipo') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID no válido.']);
        exit;
    }
    $result = $modelo->delete_equipo($id);
    echo json_encode($result);
    exit;
}

if ($accion === 'get_equipos_by_liga') {
    $id_liga = intval($_POST['id_liga'] ?? $_GET['id_liga'] ?? 0);
    if (!$id_liga) {
        echo json_encode(['success' => false, 'equipos' => []]);
        exit;
    }
    $equipos = $modelo->get_equipos_by_liga($id_liga);
    echo json_encode(['success' => true, 'equipos' => $equipos]);
    exit;
}

if ($accion === 'get_campeon') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) { echo json_encode(['success' => false, 'error' => 'ID inválido.']); exit; }
    $c = $modelo->get_campeon_by_id($id);
    if (!$c) { echo json_encode(['success' => false, 'error' => 'Campeón no encontrado.']); exit; }
    echo json_encode(['success' => true, 'campeon' => $c]);
    exit;
}

if ($accion === 'get_build_campeon') {
    $id_campeon = intval($_GET['id_campeon'] ?? 0);
    if (!$id_campeon) { echo json_encode(['success' => false, 'error' => 'ID de campeón inválido.']); exit; }
    $build = $modelo->get_build_by_campeon($id_campeon);

    if (!$build) {
        echo json_encode([
            'success'     => true,
            'tiene_build' => false,
            'build'       => null,
        ]);
        exit;
    }

    // Clasificar las runas en slots ordenados según primary_path / secondary_path
    // - runas_primarias[0]   = keystone (tipo='Primaria' del primary_path)
    // - runas_primarias[1-3] = menores  (tipo='Secundaria' del primary_path)
    // - runas_secundarias[0-1] = menores (tipo='Secundaria' del secondary_path)
    $primary_path   = intval($build['primary_path']);
    $secondary_path = intval($build['secondary_path']);
    $runas_primarias   = [null, null, null, null];
    $runas_secundarias = [null, null];
    $idx_prim_menor = 1;
    $idx_sec        = 0;
    foreach (($build['runas'] ?? []) as $r) {
        $rid     = intval($r['id_runa']);
        $tipo    = $r['tipo'] ?? '';
        $path_id = isset($r['path_id']) ? intval($r['path_id']) : 0;
        if ($tipo === 'Primaria' && $path_id === $primary_path) {
            $runas_primarias[0] = $rid;
        } elseif ($tipo === 'Secundaria' && $path_id === $primary_path) {
            if ($idx_prim_menor <= 3) $runas_primarias[$idx_prim_menor++] = $rid;
        } elseif ($tipo === 'Secundaria' && $path_id === $secondary_path) {
            if ($idx_sec <= 1) $runas_secundarias[$idx_sec++] = $rid;
        }
    }
    $build['runas_primarias']   = $runas_primarias;
    $build['runas_secundarias'] = $runas_secundarias;

    echo json_encode([
        'success'     => true,
        'tiene_build' => true,
        'build'       => $build,
    ]);
    exit;
}

if ($accion === 'update_campeon') {
    $id     = intval($_POST['id']          ?? 0);
    $nombre = trim($_POST['nombre']        ?? '');
    $rol    = trim($_POST['rol']           ?? '');
    $desc   = trim($_POST['descripcion']   ?? '');
    $foto   = trim($_POST['foto']          ?? '');
    $q      = trim($_POST['q']             ?? '');
    $w      = trim($_POST['w']             ?? '');
    $e      = trim($_POST['e']             ?? '');
    $r      = trim($_POST['r']             ?? '');
    if (!$id || !$nombre) { echo json_encode(['success' => false, 'error' => 'Nombre obligatorio.']); exit; }
    if (!$modelo->update_campeon($id, $nombre, $rol, $desc, $foto, $q, $w, $e, $r)) {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar el campeón.']);
        exit;
    }

    $nombre_build      = trim($_POST['nombre_build'] ?? '');
    $tiene_datos_build = $nombre_build !== '';

    // ESCENARIO C: no se envían datos de build → no tocar la tabla build
    if (!$tiene_datos_build) {
        echo json_encode(['success' => true]);
        exit;
    }

    $datos_build = [
        'nombre_build'   => $nombre_build,
        'popularidad'    => intval($_POST['popularidad']    ?? 80),
        'primary_path'   => intval($_POST['primary_path']   ?? 8000),
        'secondary_path' => intval($_POST['secondary_path'] ?? 8100),
        'win_rate'       => floatval($_POST['win_rate']     ?? 50.0),
        'total_matches'  => intval($_POST['total_matches']  ?? 10000),
        'obj_id'         => $_POST['obj_id']    ?? [],
        'obj_fase'       => $_POST['obj_fase']  ?? [],
        'obj_orden'      => $_POST['obj_orden'] ?? [],
        'runa_id'        => consolidar_runas_post(),
    ];

    $build_existente = $modelo->get_build_by_campeon($id);

    if ($build_existente) {
        // ESCENARIO A: build existente → UPDATE + reinsert objetos/runas
        $ok = $modelo->update_build_campeon($id, $datos_build);
    } else {
        // ESCENARIO B: sin build previa → INSERT nueva
        $ok = $modelo->crear_build_campeon($id, $datos_build);
    }

    if (!$ok) {
        echo json_encode(['success' => false, 'error' => 'Campeón actualizado, pero no se pudo guardar la build.']);
        exit;
    }

    echo json_encode(['success' => true]);
    exit;
}

if ($accion === 'get_equipo') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) { echo json_encode(['success' => false, 'error' => 'ID inválido.']); exit; }
    $eq = $modelo->get_equipo_by_id($id);
    if (!$eq) { echo json_encode(['success' => false, 'error' => 'Equipo no encontrado.']); exit; }
    $eq['roster'] = $modelo->get_roster_by_equipo($id);
    echo json_encode(['success' => true, 'equipo' => $eq]);
    exit;
}

if ($accion === 'update_equipo') {
    $id      = intval($_POST['id']          ?? 0);
    $nombre  = trim($_POST['nombre']        ?? '');
    $id_liga = intval($_POST['id_liga']     ?? 0);
    $logo    = trim($_POST['logo']          ?? '');
    $desc    = trim($_POST['descripcion']   ?? '');
    $ranking = intval($_POST['ranking']     ?? 0);
    $video   = trim($_POST['video']         ?? '');
    if (!$id || !$nombre || !$id_liga) { echo json_encode(['success' => false, 'error' => 'Nombre y liga son obligatorios.']); exit; }
    if ($logo && strpos($logo, 'http') !== 0) { echo json_encode(['success' => false, 'error' => 'URL del logo debe comenzar por http.']); exit; }
    if ($modelo->update_equipo($id, $nombre, $id_liga, $logo, $desc, $ranking, $video)) {
        // Procesar roster: 6 posiciones fijas
        $roster_data = [];
        foreach (['Top','Jungla','Mid','ADC','Support','Coach'] as $pos) {
            $roster_data[$pos] = [
                'nombre'    => trim($_POST['roster_nombre_'   . $pos] ?? ''),
                'nick'      => trim($_POST['roster_nick_'     . $pos] ?? ''),
                'foto_url'  => trim($_POST['roster_foto_url_' . $pos] ?? ''),
                'roster_id' => intval($_POST['roster_id_'     . $pos] ?? 0),
            ];
        }
        $modelo->update_roster_equipo($id, $roster_data);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar el equipo.']);
    }
    exit;
}

if ($accion === 'get_partido_admin') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) { echo json_encode(['success' => false, 'error' => 'ID inválido.']); exit; }
    $pt = $modelo->get_partido_by_id_admin($id);
    if (!$pt) { echo json_encode(['success' => false, 'error' => 'Partido no encontrado.']); exit; }
    echo json_encode(['success' => true, 'partido' => $pt]);
    exit;
}

if ($accion === 'update_partido') {
    $id         = intval($_POST['id']          ?? 0);
    $fecha      = trim($_POST['fecha']         ?? '');
    $id_eq1     = intval($_POST['id_equipo1']  ?? 0);
    $id_eq2     = intval($_POST['id_equipo2']  ?? 0);
    $res1       = intval($_POST['res1']        ?? 0);
    $res2       = intval($_POST['res2']        ?? 0);
    $stream_url = trim($_POST['stream_url']    ?? '');
    if (!$id || !$fecha || !$id_eq1 || !$id_eq2) { echo json_encode(['success' => false, 'error' => 'Datos incompletos.']); exit; }
    if ($id_eq1 === $id_eq2) { echo json_encode(['success' => false, 'error' => 'Los dos equipos deben ser diferentes.']); exit; }
    if ($modelo->update_partido($id, $fecha, $id_eq1, $id_eq2, $res1, $res2, $stream_url)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar el partido.']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
