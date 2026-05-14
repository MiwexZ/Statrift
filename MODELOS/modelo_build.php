<?php
class modelo_build
{
    private $_db;


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

    public function get_item_icon_by_riot_id(string $riot_id): string
    {
        return DDragonService::itemIcon($riot_id);
    }
}
?>
