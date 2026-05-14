<?php
class modelo_liga
{
    private $_db;

    // URLs oficiales del CDN de lolesports para ligas conocidas
    private static array $logos_cdn = [
        'LCK' => 'https://static.lolesports.com/leagues/LCK_Logo.png',
        'LEC' => 'https://static.lolesports.com/leagues/LEC-Bug_FullonDark.png',
        'LPL' => 'https://static.lolesports.com/leagues/LPL-Bug_FullonDark.png',
        'LCS' => 'https://static.lolesports.com/leagues/LCS_Logo.png',
    ];

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    public function get_liga_info(int $id): ?array
    {
        $stmt = $this->_db->prepare("SELECT * FROM liga WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
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
            $id     = intval($fila['id']);
            // Prioridad: logo de BD (ya funciona para LCK/LEC) > CDN fallback cuando está vacío
            $logo_bd  = trim($fila['logo'] ?? '');
            $logo     = $logo_bd !== ''
                ? htmlspecialchars($logo_bd)
                : htmlspecialchars(self::$logos_cdn[$fila['nombre']] ?? '');
            $delay  = ($i % 3) * 150;

            // Si no hay ningún logo, mostrar icono FontAwesome en su lugar
            $img_html = $logo
                ? "<img src='{$logo}' alt='{$nombre}' class='league-logo' loading='lazy'
                        onerror=\"this.outerHTML='<i class=\\'fa-solid fa-trophy league-logo text-gold\\' style=\\'font-size:4rem\\'></i>';this.onerror=null\">"
                : "<i class='fa-solid fa-trophy league-logo text-gold' style='font-size:4rem'></i>";

            echo "
            <div class='col-12 col-md-4 col-lg-3 mb-4' data-aos='zoom-in' data-aos-delay='{$delay}'>
                <div class='league-card bg-glass h-100 text-center'>
                    <div class='league-logo-wrap'>
                        {$img_html}
                    </div>
                    <div class='league-body'>
                        <h4 class='league-name'><i class='fa-solid fa-trophy me-2 text-gold'></i>{$nombre}</h4>
                        <a href='/VISTAS/partidos_liga.php?id={$id}' class='btn btn-gold-outline btn-sm mt-2'>
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