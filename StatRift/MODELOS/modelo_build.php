<?php
class modelo_build
{
    private $_db;

    // Mapa de iconos de objetos por nombre (CDN Riot oficial)
    private static array $item_icons = [
        'Espada Larga'          => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1036.png',
        'Vara de la Edad'       => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3118.png',
        'Botas de Movilidad'    => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3117.png',
        'Arco Recurvo'          => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1043.png',
        'Libro de la Sabiduría' => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1052.png',
        'Filo del Infinito'     => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3031.png',
        'Diente de Nashor'      => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3115.png',
        'Eclipse'               => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/6692.png',
        'Tormenta de Luden'     => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3285.png',
        'Filo de la Noche'      => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3814.png',
        'Matarracimos'          => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3085.png',
        'Piedra del Ocaso'      => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/6630.png',
        'Cañón de Fuego Rápido' => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3094.png',
        'Redencion'             => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3107.png',
        'Locket de la Solari de Hierro' => 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3190.png',
    ];

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    public function get_campeon($id)
    {
        $stmt = $this->_db->prepare("SELECT * FROM campeon WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function get_build($id_campeon)
    {
        $stmt = $this->_db->prepare("SELECT * FROM build WHERE id_campeon = ? ORDER BY popularidad DESC LIMIT 1");
        $stmt->bind_param("i", $id_campeon);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function get_build_items($id_build)
    {
        $stmt = $this->_db->prepare(
            "SELECT o.nombre FROM build_objeto bo 
             JOIN objeto o ON bo.id_objeto = o.id 
             WHERE bo.id_build = ? ORDER BY bo.orden ASC"
        );
        $stmt->bind_param("i", $id_build);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function get_build_runes($id_build)
    {
        $stmt = $this->_db->prepare(
            "SELECT r.nombre, r.tipo, r.icono FROM build_runa br 
             JOIN runa r ON br.id_runa = r.id 
             WHERE br.id_build = ?"
        );
        $stmt->bind_param("i", $id_build);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function get_item_icon($nombre)
    {
        return self::$item_icons[$nombre] ?? 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1036.png';
    }
}
?>
