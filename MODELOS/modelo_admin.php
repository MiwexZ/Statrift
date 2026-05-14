<?php
class modelo_admin
{
    private $_db;

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    // Métricas para el Dashboard
    public function get_stats(): array
    {
        $stats = [
            'usuarios' => 0,
            'publicaciones' => 0,
            'campeones' => 0,
            'partidos' => 0
        ];

        $res = $this->_db->query("SELECT COUNT(*) as c FROM jugador");
        if ($res) $stats['usuarios'] = $res->fetch_assoc()['c'];

        $res = $this->_db->query("SELECT COUNT(*) as c FROM publicaciones");
        if ($res) $stats['publicaciones'] = $res->fetch_assoc()['c'];

        $res = $this->_db->query("SELECT COUNT(*) as c FROM campeon");
        if ($res) $stats['campeones'] = $res->fetch_assoc()['c'];

        $res = $this->_db->query("SELECT COUNT(*) as c FROM partido");
        if ($res) $stats['partidos'] = $res->fetch_assoc()['c'];

        return $stats;
    }

    public function get_publicaciones()
    {
        return $this->_db->query("SELECT p.*, j.nick FROM publicaciones p LEFT JOIN jugador j ON p.id_jugador = j.id ORDER BY p.fecha DESC");
    }

    public function get_usuarios()
    {
        return $this->_db->query("SELECT id, nombre, apellidos, nick, correo, tipo, activo FROM jugador ORDER BY id DESC");
    }

    public function delete_post($id): bool
    {
        // Borrar comentarios primero por clave foránea (aunque no la haya, por limpieza)
        $stmt_c = $this->_db->prepare("DELETE FROM comentario WHERE id_publicación = ?");
        $stmt_c->bind_param("i", $id);
        $stmt_c->execute();

        $stmt = $this->_db->prepare("DELETE FROM publicaciones WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function delete_user($id): bool
    {
        // Prevenir borrar al admin
        $stmt_check = $this->_db->prepare("SELECT nick FROM jugador WHERE id = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $res = $stmt_check->get_result();
        if ($res->num_rows > 0 && $res->fetch_assoc()['nick'] === 'admin') {
            return false;
        }

        // En un caso real se borrarían dependencias, aquí lo borramos directamente
        $stmt = $this->_db->prepare("DELETE FROM jugador WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function add_champion($nombre, $q, $w, $e, $r, $foto): bool
    {
        $stmt = $this->_db->prepare("INSERT INTO campeon (nombre, q, w, e, r, foto) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $q, $w, $e, $r, $foto);
        return $stmt->execute();
    }
}
?>
