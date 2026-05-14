<?php
class modelo_campeon
{
    private $_db;

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    private function render_card(array $fila, string $delay = '0'): string
    {
        $habilidades = [
            'Q' => htmlspecialchars($fila['q'] ?? '—'),
            'W' => htmlspecialchars($fila['w'] ?? '—'),
            'E' => htmlspecialchars($fila['e'] ?? '—'),
            'R' => htmlspecialchars($fila['r'] ?? '—'),
        ];
        $id     = intval($fila['id'] ?? 0);
        $nombre = htmlspecialchars($fila['nombre'] ?? 'Campeón');
        $foto   = htmlspecialchars($fila['foto']   ?? '');
        $rol    = htmlspecialchars($fila['rol'] ?? '');
        $build_url = "/VISTAS/build.php?id={$id}";

        $rol_html = $rol ? "<small class='text-gold d-block mb-1' style='font-size:0.75rem;'><i class='fa-solid fa-crosshairs me-1'></i>{$rol}</small>" : '';

        $skills_html = '';
        foreach ($habilidades as $key => $desc) {
            $skills_html .= "
            <div class='skill-row'>
                <span class='skill-key'>{$key}</span>
                <small class='skill-desc'>{$desc}</small>
            </div>";
        }

        return "
        <div class='col-12 col-sm-6 col-lg-4 col-xl-3 mb-4' data-aos='fade-up' data-aos-delay='{$delay}'>
            <a href='{$build_url}' class='text-decoration-none'>
            <div class='champion-card bg-glass h-100' style='cursor:pointer;'>
                <div class='champion-img-wrap'>
                    <img src='{$foto}' class='champion-img' alt='{$nombre}' loading='lazy'>
                    <div class='champion-overlay'>
                        <span class='btn btn-gold btn-sm'><i class='fa-solid fa-eye me-1'></i>Ver build</span>
                    </div>
                </div>
                <div class='champion-body'>
                    {$rol_html}
                    <h4 class='champion-name'>{$nombre}</h4>
                    <div class='skills-grid'>
                        {$skills_html}
                    </div>
                </div>
            </div>
            </a>
        </div>";
    }

    public function mostrar_5_aleatorios(): void
    {
        $sql = "SELECT * FROM campeon ORDER BY RAND() LIMIT 5";
        $resultado = $this->_db->query($sql);
        if (!$resultado) return;

        echo "<h2 class='section-title text-center d-block w-100 mt-5 mb-4'>Campeones Destacados</h2>";
        echo "<div class='row justify-content-center'>";
        $i = 0;
        while ($fila = $resultado->fetch_array(MYSQLI_ASSOC)) {
            echo $this->render_card($fila, (string)($i * 100));
            $i++;
        }
        echo "</div>";
    }

    public function mostrar_todos_campeones(): void
    {
        $sql = "SELECT * FROM campeon ORDER BY nombre ASC";
        $resultado = $this->_db->query($sql);
        if (!$resultado) return;

        echo "<div class='row justify-content-center'>";
        $i = 0;
        while ($fila = $resultado->fetch_array(MYSQLI_ASSOC)) {
            echo $this->render_card($fila, (string)(($i % 4) * 100));
            $i++;
        }
        echo "</div>";
    }
}
?>