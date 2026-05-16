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

    public function get_campeones(): array
    {
        $res = $this->_db->query("SELECT id, nombre, rol, foto FROM campeon ORDER BY nombre ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function delete_champion(int $id): array
    {
        $stmt = $this->_db->prepare("SELECT COUNT(*) AS c FROM build WHERE id_campeon = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['c'];

        if ($count > 0) {
            return ['success' => false, 'error' => 'El campeón tiene builds asociadas y no puede eliminarse.'];
        }

        $stmt2 = $this->_db->prepare("DELETE FROM campeon WHERE id = ?");
        $stmt2->bind_param("i", $id);
        return $stmt2->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Error al eliminar el campeón.'];
    }

    public function get_partidos(): array
    {
        $res = $this->_db->query(
            "SELECT p.id, p.fecha, e1.nombre AS equipo1, e2.nombre AS equipo2
             FROM partido p
             JOIN juega j1 ON j1.id_partido = p.id
             JOIN equipo e1 ON e1.id = j1.id_equipo
             JOIN juega j2 ON j2.id_partido = p.id
             JOIN equipo e2 ON e2.id = j2.id_equipo
             WHERE j1.id_equipo < j2.id_equipo
             ORDER BY p.fecha DESC"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function get_equipos(): array
    {
        $res = $this->_db->query("SELECT id, nombre FROM equipo ORDER BY nombre ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function add_partido(string $fecha, int $id_equipo1, int $id_equipo2): bool
    {
        $stmt = $this->_db->prepare("INSERT INTO partido (fecha) VALUES (?)");
        if (!$stmt) return false;
        $stmt->bind_param("s", $fecha);
        if (!$stmt->execute()) { $stmt->close(); return false; }
        $id_partido = $this->_db->insert_id;
        $stmt->close();

        $stmt2 = $this->_db->prepare("INSERT INTO juega (id_partido, id_equipo, resultado) VALUES (?, ?, 0)");
        if (!$stmt2) return false;
        $stmt2->bind_param("ii", $id_partido, $id_equipo1);
        if (!$stmt2->execute()) { $stmt2->close(); return false; }

        $stmt3 = $this->_db->prepare("INSERT INTO juega (id_partido, id_equipo, resultado) VALUES (?, ?, 0)");
        if (!$stmt3) { $stmt2->close(); return false; }
        $stmt3->bind_param("ii", $id_partido, $id_equipo2);
        $ok = $stmt3->execute();
        $stmt2->close();
        $stmt3->close();
        return $ok;
    }

    public function delete_partido(int $id): bool
    {
        $stmt = $this->_db->prepare("DELETE FROM juega WHERE id_partido = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt2 = $this->_db->prepare("DELETE FROM partido WHERE id = ?");
        $stmt2->bind_param("i", $id);
        return $stmt2->execute();
    }
}
?>
