<?php
class modelo_liga
{
    private $_db;

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    public function mostrar_todas_ligas(): void
    {
        $query    = "SELECT * FROM liga ORDER BY nombre ASC";
        $resultado = $this->_db->query($query);

        if (!$resultado) {
            echo "<p class='text-danger text-center'>Error al cargar las ligas.</p>";
            return;
        }

        echo "<div class='row justify-content-center'>";
        $i = 0;
        while ($fila = $resultado->fetch_array(MYSQLI_ASSOC)) {
            $nombre = htmlspecialchars($fila['nombre'] ?? '');
            $logo   = htmlspecialchars($fila['logo']   ?? '');
            $delay  = ($i % 3) * 150;

            echo "
            <div class='col-12 col-md-4 col-lg-3 mb-4' data-aos='zoom-in' data-aos-delay='{$delay}'>
                <div class='league-card bg-glass h-100 text-center'>
                    <div class='league-logo-wrap'>
                        <img src='{$logo}' alt='{$nombre}' class='league-logo' loading='lazy'>
                    </div>
                    <div class='league-body'>
                        <h4 class='league-name'><i class='fa-solid fa-trophy me-2 text-gold'></i>{$nombre}</h4>
                        <a href='#' class='btn btn-gold-outline btn-sm mt-2'>
                            <i class='fa-solid fa-arrow-right me-1'></i>Ver partidos
                        </a>
                    </div>
                </div>
            </div>";
            $i++;
        }
        echo "</div>";
    }
}
?>