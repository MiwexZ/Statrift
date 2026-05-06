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
}
?>
