<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StatRift — Ligas & eSports</title>
    <meta name="description" content="Sigue las competiciones de League of Legends en StatRift. LCK, LEC, partidos y resultados en directo.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=1.1">
</head>
<body>
    <?php
    include "../BD/conectar_bd.php";
    include "../PHP/funciones.php";
    generar_header();
    ?>

    <!-- Hero de sección -->
    <section class="page-hero text-center" data-aos="fade-down" data-aos-duration="800">
        <div class="container">
            <div class="page-hero-icon"><i class="fa-solid fa-trophy"></i></div>
            <h1 class="page-hero-title">Ligas & eSports</h1>
            <p class="page-hero-sub">Sigue el pulso competitivo mundial. LCK, LEC y mucho más: resultados, partidos y los mejores equipos.</p>
        </div>
    </section>

    <main class="container my-5">
        <?php include "../CONTROLADORES/mostrar_todas_ligas.php"; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ once: true, offset: 50, duration: 800 });</script>
</body>
</html>