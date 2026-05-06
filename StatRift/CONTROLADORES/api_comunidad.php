<?php
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';
require_once __DIR__ . '/../MODELOS/modelo_publicacion.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['nick'])) {
    echo json_encode(['success' => false, 'error' => 'No estás autenticado.']);
    exit;
}

$modelo = new modelo_publicacion();
$accion = $_POST['accion'] ?? '';

if ($accion === 'publicar') {
    $titulo = trim($_POST['titulo'] ?? '');
    $cuerpo = trim($_POST['cuerpo'] ?? '');
    $nick   = $_SESSION['nick'];

    if (empty($titulo) || empty($cuerpo)) {
        echo json_encode(['success' => false, 'error' => 'El título y el cuerpo son obligatorios.']);
        exit;
    }

    $resultado = $modelo->crear_publicacion($nick, $titulo, $cuerpo);
    if ($resultado) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar la publicación.']);
    }
    exit;
}

if ($accion === 'comentar') {
    $id_publicacion = intval($_POST['id_publicacion'] ?? 0);
    $cuerpo         = trim($_POST['cuerpo'] ?? '');
    $nick           = $_SESSION['nick'];

    if ($id_publicacion <= 0 || empty($cuerpo)) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos para comentar.']);
        exit;
    }

    $resultado = $modelo->crear_comentario($nick, $id_publicacion, $cuerpo);
    if ($resultado) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar el comentario.']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
