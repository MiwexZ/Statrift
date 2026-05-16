<?php
ob_start();
session_start();
require_once __DIR__ . '/../BD/conectar_bd.php';
require_once __DIR__ . '/../PHP/funciones.php';
require_once __DIR__ . '/../MODELOS/modelo_publicacion.php';

$modeloComunidad = new modelo_publicacion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidad - StatRift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Quill Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css?v=1.7">
</head>
<body>
    <?php generar_header(); ?>

    <section class="page-hero text-center text-light d-flex align-items-center justify-content-center flex-column">
        <div class="hero-icon-wrap" data-aos="zoom-in" data-aos-duration="1000">
            <i class="fa-solid fa-users hero-icon text-gold"></i>
        </div>
        <h1 class="page-hero-title mt-4" data-aos="fade-up" data-aos-delay="200">El Nexo Social</h1>
        <p class="page-hero-sub mt-3" data-aos="fade-up" data-aos-delay="400">
            Comparte tus jugadas, debate sobre el parche actual y conecta con otros jugadores.
        </p>
    </section>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                
                <!-- CREAR PUBLICACIÓN (Solo Logueados) -->
                <?php if (isset($_SESSION['nick'])): ?>
                <div class="create-post-box bg-glass p-4 rounded-4 mb-5" data-aos="fade-up">
                    <h4 class="text-gold mb-3"><i class="fa-solid fa-pen-nib me-2"></i>Crear Publicación</h4>
                    <form id="formPublicacion">
                        <input type="text" id="post-title" class="form-control bg-dark text-light border-secondary mb-3" placeholder="Título de tu publicación..." required>
                        <div id="editor-container" class="bg-dark text-light border-secondary" style="height: 150px; border-radius: 0 0 8px 8px;"></div>
                        <button type="submit" class="btn btn-gold mt-3 float-end" id="btn-publicar">
                            <i class="fa-solid fa-paper-plane me-2"></i>Publicar
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
                <?php else: ?>
                <div class="alert alert-dark border-gold text-center mb-5" data-aos="fade-up">
                    <i class="fa-solid fa-lock text-gold mb-2 fs-3 d-block"></i>
                    Para poder crear una publicación o comentar necesitas <a href="/VISTAS/login.php" class="text-gold fw-bold">iniciar sesión</a>.
                </div>
                <?php endif; ?>

                <!-- FEED DE PUBLICACIONES -->
                <div id="community-feed">
                    <?php $modeloComunidad->mostrar_comunidad(); ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        AOS.init({ once: true });

        // Inicializar Quill Editor si existe el contenedor (usuario logueado)
        let quill = null;
        if (document.getElementById('editor-container')) {
            quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: '¿Qué tienes en mente?',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });

            // Override Quill dark mode styles (basic patch)
            document.querySelector('.ql-toolbar').style.backgroundColor = 'rgba(255,255,255,0.05)';
            document.querySelector('.ql-toolbar').style.border = '1px solid #6c757d';
            document.querySelector('.ql-toolbar').style.borderBottom = 'none';
            document.querySelector('.ql-toolbar').style.borderRadius = '8px 8px 0 0';
        }

        // AJAX POST PUBLICACIÓN
        const formPub = document.getElementById('formPublicacion');
        if (formPub) {
            formPub.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btn-publicar');
                const titulo = document.getElementById('post-title').value;
                const cuerpo = quill.root.innerHTML;

                if (!titulo || quill.getText().trim().length === 0) {
                    alert('Por favor, rellena el título y el contenido.');
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Publicando...';

                const formData = new FormData();
                formData.append('accion', 'publicar');
                formData.append('titulo', titulo);
                formData.append('cuerpo', cuerpo);

                fetch('/CONTROLADORES/api_comunidad.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Recarga simple para ver la publicación nueva
                    } else {
                        alert(data.error || 'Error desconocido.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Publicar';
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error en la conexión AJAX.');
                    btn.disabled = false;
                });
            });
        }

        // AJAX POST COMENTARIO
        document.querySelectorAll('.btn-comment').forEach(btn => {
            btn.addEventListener('click', function() {
                const idPub = this.getAttribute('data-post-id');
                const input = document.querySelector(`.comment-input[data-post-id='${idPub}']`);
                const cuerpo = input.value.trim();

                if (cuerpo.length === 0) return;

                this.disabled = true;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                const formData = new FormData();
                formData.append('accion', 'comentar');
                formData.append('id_publicacion', idPub);
                formData.append('cuerpo', cuerpo);

                fetch('/CONTROLADORES/api_comunidad.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Recarga para ver el comentario
                    } else {
                        alert(data.error || 'Error al comentar.');
                        this.disabled = false;
                        this.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error en la conexión AJAX.');
                    this.disabled = false;
                });
            });
        });
        
        // Comentar pulsando ENTER
        document.querySelectorAll('.comment-input').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const idPub = this.getAttribute('data-post-id');
                    document.querySelector(`.btn-comment[data-post-id='${idPub}']`).click();
                }
            });
        });

    </script>
</body>
</html>
