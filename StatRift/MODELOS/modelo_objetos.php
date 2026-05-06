<?php
class modelo_objeto
{
    private $_db;

    // Mapa de fallback: si la BD no tiene foto, usamos el CDN directamente
    private static array $fallback = [
        'Espada Larga'          => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1036.png',
        'Vara de la Edad'       => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3118.png',
        'Botas de Movilidad'    => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3117.png',
        'Arco Recurvo'          => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1043.png',
        'Libro de la Sabiduría' => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1052.png',
    ];

    public function __construct()
    {
        $this->_db = conectar::conexion();
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

            // Usa foto de BD si existe, sino el fallback por nombre
            $foto = !empty($fila['foto'])
                ? htmlspecialchars($fila['foto'])
                : (self::$fallback[$fila['nombre']] ?? 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1036.png');

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