<?php
require_once __DIR__ . '/../PHP/DDragonService.php';

class modelo_objeto
{
    private $_db;

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    private function resolver_icono(array $fila): string
    {
        if (!empty($fila['riot_id'])) {
            return DDragonService::itemIcon((string)$fila['riot_id']);
        }
        if (!empty($fila['foto'])) {
            return htmlspecialchars($fila['foto']);
        }
        $v = DDragonService::getVersion();
        return "https://ddragon.leagueoflegends.com/cdn/{$v}/img/item/1036.png";
    }

    public function mostrar_objetos_aleatorios(): void
    {
        $sql = "SELECT * FROM objeto ORDER BY RAND() LIMIT 5";
        $resultado = $this->_db->query($sql);

        if (!$resultado) return;

        echo "<h2 class='section-title text-center d-block w-100 mt-5 mb-4'>Objetos Místicos</h2>";
        echo "<div class='row justify-content-center'>";

        $i = 0;
        while ($fila = $resultado->fetch_array(MYSQLI_ASSOC)) {
            $nombre = htmlspecialchars($fila['nombre'] ?? '');
            $foto   = $this->resolver_icono($fila);

            $delay = ($i % 5) * 100;

            echo "
            <div class='col-6 col-md-4 col-lg-2 mb-4' data-aos='zoom-in' data-aos-delay='{$delay}'>
                <div class='item-card bg-glass h-100 text-center'>
                    <div class='item-icon-wrap'>
                        <img src='{$foto}' alt='{$nombre}' class='item-icon' loading='lazy'>
                    </div>
                    <div class='item-body'>
                        <h6 class='item-name'>{$nombre}</h6>
                    </div>
                </div>
            </div>";
            $i++;
        }
        echo "</div>";
    }
}
?>