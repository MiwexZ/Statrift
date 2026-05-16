<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';

$error = intval($_GET['error'] ?? 0);
$errores = [
    1 => 'Todos los campos son obligatorios.',
    2 => 'El nombre de usuario "admin" está reservado.',
    3 => 'La contraseña debe tener al menos 6 caracteres.',
    4 => 'Las contraseñas no coinciden.',
    5 => 'Ese nombre de usuario ya está en uso.',
    6 => 'Ese correo electrónico ya está registrado.',
    7 => 'Error al crear la cuenta. Inténtalo de nuevo.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse — StatRift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=1.7">
</head>
<body>
<?php generar_header(); ?>

<main class="container py-5" style="max-width:540px">
    <div class="bg-glass p-4 p-md-5 rounded-4" data-aos="fade-up">

        <div class="text-center mb-4">
            <i class="fa-solid fa-user-plus text-gold" style="font-size:2.5rem"></i>
            <h2 class="text-gold fw-bold mt-2 mb-1">Crear Cuenta</h2>
            <p class="text-muted small">Únete a la comunidad StatRift</p>
        </div>

        <?php if ($error > 0 && isset($errores[$error])): ?>
            <div class="alert py-2 text-center small border-0 mb-3"
                 style="background:rgba(239,68,68,0.15);color:#fca5a5;border-radius:8px;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i><?= $errores[$error] ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/CONTROLADORES/procesar_registro.php" novalidate>
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label text-muted small">Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                           placeholder="Tu nombre" required autocomplete="given-name">
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label text-muted small">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control"
                           placeholder="Tus apellidos" required autocomplete="family-name">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small">Nick <span class="text-muted">(nombre de usuario)</span></label>
                <input type="text" name="nick" class="form-control"
                       placeholder="Ej: Faker99" required maxlength="20" autocomplete="username">
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small">Correo electrónico</label>
                <input type="email" name="correo" class="form-control"
                       placeholder="correo@ejemplo.com" required autocomplete="email">
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small">
                    Contraseña <span class="text-muted">(mín. 6 caracteres)</span>
                </label>
                <input type="password" name="pass" class="form-control"
                       placeholder="••••••••" required autocomplete="new-password">
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small">Confirmar contraseña</label>
                <input type="password" name="confirmar" class="form-control"
                       placeholder="••••••••" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-gold w-100 py-2 fw-bold">
                <i class="fa-solid fa-user-plus me-2"></i>Crear cuenta
            </button>
        </form>

        <p class="text-center mt-3 mb-0 small text-muted">
            ¿Ya tienes cuenta?
            <a href="/VISTAS/login.php" class="text-gold text-decoration-none fw-bold">Inicia sesión</a>
        </p>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once: true, offset: 40, duration: 700 });</script>
</body>
</html>
