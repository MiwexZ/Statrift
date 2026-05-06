<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statrift - Premium</title>
    <!-- Enlace a las bibliotecas de Bootstrap 5, FontAwesome y AOS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css?v=1.1">
</head>

<body>
    <?php
    include "BD/conectar_bd.php";
    include "PHP/funciones.php";
    $conexion = conectar::conexion();
    conectar::desconectar($conexion);
    generar_header();
    ?>
    <main class="container my-5">
        <section class="hero-section bg-glass text-center" data-aos="zoom-in" data-aos-duration="1000">
            <h1 class="hero-title">Domina la Grieta</h1>
            <p class="hero-subtitle">Descubre los mejores campeones, analiza partidos, lee publicaciones destacadas y encuentra los objetos perfectos para tu próximo combate.</p>
        </section>

        <div data-aos="fade-up" data-aos-delay="200">
            <?php include "CONTROLADORES/mostrar_5_ale.php"; ?>
        </div>
        <div data-aos="fade-up" data-aos-delay="300">
            <?php include "CONTROLADORES/mostrar_publi_aleatoria.php"; ?>
        </div>
        <div data-aos="fade-up" data-aos-delay="400">
            <?php include "CONTROLADORES/mostrar_partidos_semana.php"; ?>
        </div>
        <div data-aos="fade-up" data-aos-delay="500">
            <?php include "CONTROLADORES/mostrar_objetos_aleatorios.php"; ?>
        </div>
    </main>
    
    <!-- Scripts: Bootstrap 5 (sin jQuery) y AOS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            offset: 50,
            duration: 800
        });
    </script>
</body>

</html>