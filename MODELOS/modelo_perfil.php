<?php
class modelo_perfil
{
    private $_db;

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    public function get_user_data($nick)
    {
        $stmt = $this->_db->prepare("SELECT j.*, e.nombre as equipo_favorito_nombre 
                                     FROM jugador j 
                                     LEFT JOIN equipo e ON j.id_equipo_favorito = e.id 
                                     WHERE j.nick = ?");
        $stmt->bind_param("s", $nick);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    public function get_equipos()
    {
        return $this->_db->query("SELECT id, nombre FROM equipo ORDER BY nombre ASC");
    }

    public function get_user_posts($id_jugador)
    {
        $stmt = $this->_db->prepare("SELECT * FROM publicaciones WHERE id_jugador = ? ORDER BY fecha DESC, hora DESC");
        $stmt->bind_param("i", $id_jugador);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function update_profile($id_jugador, $id_equipo, $pass_nueva)
    {
        if (!empty($pass_nueva)) {
            $hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
            $stmt = $this->_db->prepare("UPDATE jugador SET id_equipo_favorito = ?, pass = ? WHERE id = ?");
            $stmt->bind_param("isi", $id_equipo, $hash, $id_jugador);
        } else {
            $stmt = $this->_db->prepare("UPDATE jugador SET id_equipo_favorito = ? WHERE id = ?");
            $stmt->bind_param("ii", $id_equipo, $id_jugador);
        }
        return $stmt->execute();
    }

    public function delete_own_post($id_jugador, $id_post)
    {
        // Verificar que el post le pertenece
        $stmt_check = $this->_db->prepare("SELECT id FROM publicaciones WHERE id = ? AND id_jugador = ?");
        $stmt_check->bind_param("ii", $id_post, $id_jugador);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows === 0) return false;

        // Borrar comentarios
        $stmt_c = $this->_db->prepare("DELETE FROM comentario WHERE id_publicación = ?");
        $stmt_c->bind_param("i", $id_post);
        $stmt_c->execute();

        // Borrar post
        $stmt = $this->_db->prepare("DELETE FROM publicaciones WHERE id = ? AND id_jugador = ?");
        $stmt->bind_param("ii", $id_post, $id_jugador);
        return $stmt->execute();
    }

    public function delete_own_account(int $id): bool
    {
        // 1) Protección admin: nunca borrar la cuenta con nick 'admin'.
        $s0 = $this->_db->prepare("SELECT nick FROM jugador WHERE id = ?");
        if (!$s0) {
            error_log('[DELETE_ACCOUNT] prepare SELECT nick failed: ' . $this->_db->error);
            return false;
        }
        $s0->bind_param("i", $id);
        $s0->execute();
        $row = $s0->get_result()->fetch_assoc();
        $s0->close();
        if (!$row || ($row['nick'] ?? '') === 'admin') return false;

        // 2) Borrar comentarios propios del usuario.
        $s1 = $this->_db->prepare("DELETE FROM comentario WHERE id_jugador = ?");
        if (!$s1) {
            error_log('[DELETE_ACCOUNT] prepare DELETE comentario propios failed: ' . $this->_db->error);
            return false;
        }
        $s1->bind_param("i", $id);
        if (!$s1->execute()) {
            error_log('[DELETE_ACCOUNT] execute DELETE comentario propios failed: ' . $s1->error);
            $s1->close();
            return false;
        }
        $s1->close();

        // 3) Borrar comentarios de otros sobre las publicaciones del usuario.
        //    NOTA: la columna FK lleva tilde literal: `id_publicación`.
        $s2 = $this->_db->prepare(
            "DELETE FROM comentario
             WHERE `id_publicación` IN (SELECT id FROM publicaciones WHERE id_jugador = ?)"
        );
        if (!$s2) {
            error_log('[DELETE_ACCOUNT] prepare DELETE comentario sobre pubs failed: ' . $this->_db->error);
            return false;
        }
        $s2->bind_param("i", $id);
        if (!$s2->execute()) {
            error_log('[DELETE_ACCOUNT] execute DELETE comentario sobre pubs failed: ' . $s2->error);
            $s2->close();
            return false;
        }
        $s2->close();

        // 4) Borrar las publicaciones del usuario.
        $s3 = $this->_db->prepare("DELETE FROM publicaciones WHERE id_jugador = ?");
        if (!$s3) {
            error_log('[DELETE_ACCOUNT] prepare DELETE publicaciones failed: ' . $this->_db->error);
            return false;
        }
        $s3->bind_param("i", $id);
        if (!$s3->execute()) {
            error_log('[DELETE_ACCOUNT] execute DELETE publicaciones failed: ' . $s3->error);
            $s3->close();
            return false;
        }
        $s3->close();

        // 5) Borrar el jugador con doble cinturón AND nick != 'admin'.
        $s4 = $this->_db->prepare("DELETE FROM jugador WHERE id = ? AND nick != 'admin'");
        if (!$s4) {
            error_log('[DELETE_ACCOUNT] prepare DELETE jugador failed: ' . $this->_db->error);
            return false;
        }
        $s4->bind_param("i", $id);
        if (!$s4->execute()) {
            error_log('[DELETE_ACCOUNT] execute DELETE jugador failed: ' . $s4->error);
            $s4->close();
            return false;
        }
        $afectadas = $s4->affected_rows;
        $s4->close();

        return $afectadas > 0;
    }
}
?>
