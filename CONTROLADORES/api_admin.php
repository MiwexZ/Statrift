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
$accion = $_POST['accion'] ?? '';

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
    $nombre = trim($_POST['nombre'] ?? '');
    $q = trim($_POST['q'] ?? '');
    $w = trim($_POST['w'] ?? '');
    $e = trim($_POST['e'] ?? '');
    $r = trim($_POST['r'] ?? '');
    $foto = trim($_POST['foto'] ?? '');

    if (!$nombre || !$foto) {
        echo json_encode(['success' => false, 'error' => 'Nombre y URL de foto son obligatorios.']);
        exit;
    }

    if ($modelo->add_champion($nombre, $q, $w, $e, $r, $foto)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al añadir el campeón.']);
    }
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

echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
