<?php
class modelo_registro
{
    public static function nick_existe(string $nick, mysqli $db): bool
    {
        $stmt = $db->prepare("SELECT COUNT(*) FROM jugador WHERE nick = ?");
        $stmt->bind_param("s", $nick);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    public static function correo_existe(string $correo, mysqli $db): bool
    {
        $stmt = $db->prepare("SELECT COUNT(*) FROM jugador WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    public static function crear_usuario(
        string $nombre, string $apellidos, string $nick,
        string $pass_hash, string $correo, mysqli $db
    ): bool {
        $stmt = $db->prepare(
            "INSERT INTO jugador (nombre, apellidos, nick, pass, correo, tipo, activo, id_equipo_favorito)
             VALUES (?, ?, ?, ?, ?, 'Jugador', 1, NULL)"
        );
        $stmt->bind_param("sssss", $nombre, $apellidos, $nick, $pass_hash, $correo);
        return $stmt->execute();
    }
}
?>
