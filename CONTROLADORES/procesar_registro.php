<?php
ob_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../MODELOS/modelo_registro.php';

$db = conectar::conexion();

$nombre    = trim($_POST['nombre']    ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$nick      = trim($_POST['nick']      ?? '');
$correo    = trim($_POST['correo']    ?? '');
$pass      = $_POST['pass']           ?? '';
$confirmar = $_POST['confirmar']      ?? '';

// 1. Campos vacíos
if (!$nombre || !$apellidos || !$nick || !$correo || !$pass || !$confirmar) {
    header("Location: /VISTAS/registro.php?error=1"); exit;
}
// 2. Nick reservado
if (strtolower($nick) === 'admin') {
    header("Location: /VISTAS/registro.php?error=2"); exit;
}
// 3. Contraseña mínimo 6 caracteres
if (strlen($pass) < 6) {
    header("Location: /VISTAS/registro.php?error=3"); exit;
}
// 4. Contraseñas no coinciden
if ($pass !== $confirmar) {
    header("Location: /VISTAS/registro.php?error=4"); exit;
}
// 5. Nick ya existe
if (modelo_registro::nick_existe($nick, $db)) {
    header("Location: /VISTAS/registro.php?error=5"); exit;
}
// 6. Correo ya existe
if (modelo_registro::correo_existe($correo, $db)) {
    header("Location: /VISTAS/registro.php?error=6"); exit;
}
// 7. Insertar usuario
$hash = password_hash($pass, PASSWORD_BCRYPT);
if (modelo_registro::crear_usuario($nombre, $apellidos, $nick, $hash, $correo, $db)) {
    header("Location: /VISTAS/login.php?registro=ok"); exit;
} else {
    header("Location: /VISTAS/registro.php?error=7"); exit;
}
?>
