<?php
class modelo_publicacion
{
    private $_db;

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    // Helper: Obtener ID del jugador a partir de su nick
    private function get_id_jugador(string $nick): int
    {
        $stmt = $this->_db->prepare("SELECT id FROM jugador WHERE nick = ?");
        $stmt->bind_param("s", $nick);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            return (int) $res->fetch_assoc()['id'];
        }
        return 0;
    }

    // Crear nueva publicación
    public function crear_publicacion(string $nick, string $titulo, string $cuerpo): bool
    {
        $id_jugador = $this->get_id_jugador($nick);
        if ($id_jugador === 0) return false;

        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        
        $stmt = $this->_db->prepare("INSERT INTO publicaciones (titulo, cuerpo, fecha, hora, id_jugador) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $titulo, $cuerpo, $fecha, $hora, $id_jugador);
        return $stmt->execute();
    }

    // Crear nuevo comentario
    public function crear_comentario(string $nick, int $id_publicacion, string $cuerpo): bool
    {
        $id_jugador = $this->get_id_jugador($nick);
        if ($id_jugador === 0) return false;

        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        
        // Cuidado: la columna en la BD tiene tilde "id_publicación" en lugar de "id_publicacion"
        $stmt = $this->_db->prepare("INSERT INTO comentario (cuerpo, fecha, hora, id_publicación, id_jugador) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $cuerpo, $fecha, $hora, $id_publicacion, $id_jugador);
        return $stmt->execute();
    }

    // Mostrar todas las publicaciones y sus comentarios
    public function mostrar_comunidad(): void
    {
        // Obtener publicaciones con nombre del autor
        $sql = "SELECT p.*, j.nick, j.nombre 
                FROM publicaciones p 
                LEFT JOIN jugador j ON p.id_jugador = j.id 
                ORDER BY p.fecha DESC, p.hora DESC";
        $resultado = $this->_db->query($sql);

        if (!$resultado || $resultado->num_rows === 0) {
            echo "<div class='text-center text-muted my-5'><i class='fa-solid fa-ghost fs-1 mb-3'></i><br>No hay publicaciones aún. ¡Sé el primero!</div>";
            return;
        }

        while ($fila = $resultado->fetch_assoc()) {
            $id_pub = $fila['id'];
            $titulo = htmlspecialchars($fila['titulo']);
            // Permitimos HTML seguro del WYSIWYG (strip_tags bloquea cosas raras pero deja lo básico)
            $cuerpo = strip_tags($fila['cuerpo'], '<b><i><u><strong><em><p><br><ul><li><ol><h1><h2><h3><h4><h5><h6><a>');
            $fecha  = $fila['fecha'];
            $hora   = substr($fila['hora'], 0, 5); // HH:MM
            $autor  = htmlspecialchars($fila['nick'] ?? 'Desconocido');
            $inicial = strtoupper(substr($autor, 0, 1));

            // Componente de Publicación
            echo "
            <div class='community-post bg-glass p-4 rounded-4 mb-4' data-aos='fade-up'>
                <div class='d-flex align-items-center mb-3'>
                    <div class='post-avatar me-3'>{$inicial}</div>
                    <div>
                        <h5 class='text-gold mb-0 fw-bold'>{$autor}</h5>
                        <small class='text-muted'><i class='fa-regular fa-clock me-1'></i> {$fecha} a las {$hora}</small>
                    </div>
                </div>
                <h3 class='post-title mb-3'>{$titulo}</h3>
                <div class='post-body text-light mb-4'>
                    {$cuerpo}
                </div>
                
                <hr class='border-secondary opacity-25'>
                
                <div class='comments-section'>
                    <h6 class='text-gold mb-3'><i class='fa-regular fa-comments me-2'></i> Comentarios</h6>
                    <div class='comments-list mb-3' id='comments-list-{$id_pub}'>";

            // Obtener comentarios para esta publicación (nota: id_publicación con tilde)
            $sql_com = "SELECT c.*, j.nick 
                        FROM comentario c 
                        LEFT JOIN jugador j ON c.id_jugador = j.id 
                        WHERE c.id_publicación = ? 
                        ORDER BY c.fecha ASC, c.hora ASC";
            $stmt_com = $this->_db->prepare($sql_com);
            $stmt_com->bind_param("i", $id_pub);
            $stmt_com->execute();
            $res_com = $stmt_com->get_result();

            if ($res_com->num_rows === 0) {
                echo "<p class='text-muted small ms-2 empty-comments'>No hay comentarios aún.</p>";
            } else {
                while ($com = $res_com->fetch_assoc()) {
                    $c_cuerpo = htmlspecialchars($com['cuerpo']);
                    $c_autor = htmlspecialchars($com['nick'] ?? 'Usuario');
                    $c_fecha = $com['fecha'] . " " . substr($com['hora'], 0, 5);
                    $c_ini = strtoupper(substr($c_autor, 0, 1));

                    echo "
                    <div class='comment-item d-flex mb-3'>
                        <div class='comment-avatar me-2 mt-1'>{$c_ini}</div>
                        <div class='comment-content bg-dark-subtle p-2 px-3 rounded-3 w-100'>
                            <div class='d-flex justify-content-between align-items-baseline'>
                                <span class='fw-bold text-gold small'>{$c_autor}</span>
                                <span class='text-muted' style='font-size:0.7rem'>{$c_fecha}</span>
                            </div>
                            <div class='text-light small mt-1'>{$c_cuerpo}</div>
                        </div>
                    </div>";
                }
            }
            echo "  </div>"; // Fin comments-list

            // Caja para escribir comentario (solo si logueado)
            if (isset($_SESSION['nick'])) {
                echo "
                    <div class='comment-input-box d-flex mt-2'>
                        <input type='text' class='form-control form-control-sm bg-dark text-light border-secondary comment-input' placeholder='Escribe un comentario...' data-post-id='{$id_pub}'>
                        <button class='btn btn-gold btn-sm ms-2 btn-comment' data-post-id='{$id_pub}'><i class='fa-solid fa-paper-plane'></i></button>
                    </div>";
            } else {
                echo "<p class='text-muted small mt-2'><a href='/statrift/VISTAS/login.php' class='text-gold'>Inicia sesión</a> para comentar.</p>";
            }

            echo "
                </div>
            </div>"; // Fin community-post
        }
    }

    // Método para la página principal (index.php)
    public function publicacion_aleatoria()
    {
        $sql = "SELECT p.*, j.nombre FROM publicaciones p LEFT JOIN jugador j ON p.id_jugador = j.id ORDER BY RAND() LIMIT 1";
        $resultado = $this->_db->query($sql);

        if (!$resultado || $resultado->num_rows === 0) return;

        $fila = $resultado->fetch_assoc();
        $titulo = htmlspecialchars($fila['titulo']);
        $cuerpo = strip_tags($fila['cuerpo'], '<b><i><u><strong><em><p><br>');
        $fecha = $fila['fecha'];
        $autor = htmlspecialchars($fila['nombre'] ?? 'Desconocido');

        echo "
        <h2 class='section-title text-center mt-5 mb-4 d-block'>Noticia Destacada</h2>
        <div class='bg-glass text-center p-5 mx-auto rounded-lg mb-5' style='max-width: 800px;'>
            <h3 class='text-gold mb-3' style='font-size: 2.5rem; font-weight: 700;'>{$titulo}</h3>
            <div class='text-light mb-4' style='font-size: 1.1rem; line-height: 1.8;'>{$cuerpo}</div>
            <div class='d-flex justify-content-center align-items-center mt-4 pt-4 border-top' style='border-color: rgba(255,255,255,0.1) !important;'>
                <small class='text-muted me-3'><i class='far fa-calendar-alt'></i> {$fecha}</small>
                <small class='text-gold fw-bold'><i class='fas fa-user-edit'></i> {$autor}</small>
            </div>
        </div>
        ";
    }
}
?>