<?php
ob_start();
session_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../MODELOS/modelo_perfil.php';

header('Content-Type: application/json');

if (!isset($_SESSION['nick'])) {
    $out = ob_get_clean();
    echo json_encode(['success' => false, 'error' => 'No estás autenticado.', 'php_out' => $out]);
    exit;
}

$modelo = new modelo_perfil();
$accion = $_POST['accion'] ?? '';

if ($accion === 'update_profile') {
    $nick = $_SESSION['nick'];
    $user = $modelo->get_user_data($nick);
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado.']);
        exit;
    }

    $id_equipo = intval($_POST['id_equipo'] ?? 0);
    $pass = trim($_POST['pass_nueva'] ?? '');

    // Si el id_equipo es 0, lo ponemos a NULL
    if ($id_equipo === 0) $id_equipo = null;

    if ($modelo->update_profile($user['id'], $id_equipo, $pass)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar el perfil.']);
    }
    exit;
}

if ($accion === 'delete_my_post') {
    $nick = $_SESSION['nick'];
    $user = $modelo->get_user_data($nick);
    $id_post = intval($_POST['id_post'] ?? 0);

    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado.']);
        exit;
    }

    if ($modelo->delete_own_post($user['id'], $id_post)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar o no tienes permiso.']);
    }
    exit;
}

if ($accion === 'delete_account') {
    if (!isset($_SESSION['nick'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
        exit;
    }

    // Doble protección admin: chequeo de sesión + el modelo rechaza el nick 'admin'.
    if ($_SESSION['nick'] === 'admin') {
        echo json_encode(['success' => false, 'error' => 'El administrador no puede eliminar su cuenta']);
        exit;
    }

    // El ID se obtiene de BD usando el nick de sesión — nunca de $_POST.
    $nick = $_SESSION['nick'];
    $user = $modelo->get_user_data($nick);
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado.']);
        exit;
    }

    $id = (int)$user['id'];
    $ok = $modelo->delete_own_account($id);

    if ($ok) {
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        if (isset($_COOKIE['usuario'])) {
            setcookie('usuario', '', time() - 3600, '/');
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la cuenta']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
