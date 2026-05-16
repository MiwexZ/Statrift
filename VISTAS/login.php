<?php ob_start(); session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous" />
    <!-- Link cdn FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../CSS/style.css?v=1.1">
    <title>Document</title>
</head>

<body>
    <?php
    include "../BD/conectar_bd.php";
    include "../PHP/funciones.php";
    $conexion = conectar::conexion();
    // generar_header();
    ?>
    <main>
        <?php
        include("../CONTROLADORES/generar_login.php");
        conectar::desconectar($conexion);
        ?>
        <?php if (($_GET['registro'] ?? '') === 'ok'): ?>
            <p class="text-center mt-2" style="color:#4ade80;font-size:.95rem;">
                <i class="fa-solid fa-circle-check me-1"></i>Cuenta creada correctamente. Ya puedes iniciar sesión.
            </p>
        <?php endif; ?>
        <p class="text-center mt-3" style="color:#C8A96E;">
            ¿No tienes cuenta?
            <a href="/VISTAS/registro.php" style="color:#C8A96E;font-weight:600;">Regístrate aquí</a>
        </p>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <!-- Enlace a las bibliotecas de jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper-base.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>

</html>