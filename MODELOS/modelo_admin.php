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

        $res = $this->_db->query("SELECT COUNT(*) as c FROM equipo");
        if ($res) $stats['equipos'] = $res->fetch_assoc()['c'];

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

    public function tiene_publicaciones(int $id): int
    {
        $stmt = $this->_db->prepare("SELECT COUNT(*) FROM publicaciones WHERE id_jugador = ?");
        if (!$stmt) {
            error_log('[modelo_admin::tiene_publicaciones] prepare failed: ' . $this->_db->error);
            return 0;
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            error_log('[modelo_admin::tiene_publicaciones] execute failed: ' . $stmt->error);
            $stmt->close();
            return 0;
        }
        $row = $stmt->get_result()->fetch_row();
        $stmt->close();
        return (int)($row[0] ?? 0);
    }

    public function delete_user(int $id): bool
    {
        // Doble protección admin: SELECT previo + cláusula AND nick != 'admin' en el DELETE final.
        $stmt_check = $this->_db->prepare("SELECT nick FROM jugador WHERE id = ?");
        if (!$stmt_check) {
            error_log('[modelo_admin::delete_user] prepare SELECT nick failed: ' . $this->_db->error);
            return false;
        }
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $res = $stmt_check->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt_check->close();
        if (!$row) return false;
        if (($row['nick'] ?? '') === 'admin') return false;

        // 1) Borrar comentarios propios del usuario.
        $s1 = $this->_db->prepare("DELETE FROM comentario WHERE id_jugador = ?");
        if (!$s1) {
            error_log('[modelo_admin::delete_user] prepare DELETE comentario propios failed: ' . $this->_db->error);
            return false;
        }
        $s1->bind_param("i", $id);
        if (!$s1->execute() || $this->_db->errno) {
            error_log('[modelo_admin::delete_user] execute DELETE comentario propios failed: ' . $s1->error);
            $s1->close();
            return false;
        }
        $s1->close();

        // 2) Borrar comentarios de OTROS sobre las publicaciones del usuario.
        //    NOTA: la columna FK lleva tilde literal: `id_publicación`.
        $s2 = $this->_db->prepare(
            "DELETE FROM comentario
             WHERE `id_publicación` IN (SELECT id FROM publicaciones WHERE id_jugador = ?)"
        );
        if (!$s2) {
            error_log('[modelo_admin::delete_user] prepare DELETE comentario sobre pubs failed: ' . $this->_db->error);
            return false;
        }
        $s2->bind_param("i", $id);
        if (!$s2->execute() || $this->_db->errno) {
            error_log('[modelo_admin::delete_user] execute DELETE comentario sobre pubs failed: ' . $s2->error);
            $s2->close();
            return false;
        }
        $s2->close();

        // 3) Borrar las publicaciones del usuario.
        $s3 = $this->_db->prepare("DELETE FROM publicaciones WHERE id_jugador = ?");
        if (!$s3) {
            error_log('[modelo_admin::delete_user] prepare DELETE publicaciones failed: ' . $this->_db->error);
            return false;
        }
        $s3->bind_param("i", $id);
        if (!$s3->execute() || $this->_db->errno) {
            error_log('[modelo_admin::delete_user] execute DELETE publicaciones failed: ' . $s3->error);
            $s3->close();
            return false;
        }
        $s3->close();

        // 4) Borrar el jugador con doble cinturón AND nick != 'admin'.
        $s4 = $this->_db->prepare("DELETE FROM jugador WHERE id = ? AND nick != 'admin'");
        if (!$s4) {
            error_log('[modelo_admin::delete_user] prepare DELETE jugador failed: ' . $this->_db->error);
            return false;
        }
        $s4->bind_param("i", $id);
        if (!$s4->execute() || $this->_db->errno) {
            error_log('[modelo_admin::delete_user] execute DELETE jugador failed: ' . $s4->error);
            $s4->close();
            return false;
        }
        $afectadas = $s4->affected_rows;
        $s4->close();

        return $afectadas > 0;
    }

    public function add_champion(string $nombre, string $q, string $w, string $e, string $r, string $foto, string $rol, string $descripcion): int
    {
        $stmt = $this->_db->prepare("INSERT INTO campeon (nombre, q, w, e, r, foto, rol, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return 0;
        $stmt->bind_param("ssssssss", $nombre, $q, $w, $e, $r, $foto, $rol, $descripcion);
        if (!$stmt->execute()) return 0;
        return (int)$this->_db->insert_id;
    }

    public function add_build(int $id_campeon, string $nombre_build, int $popularidad, int $primary_path, int $secondary_path, float $win_rate, int $total_matches): int
    {
        $stmt = $this->_db->prepare(
            "INSERT INTO build (id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return 0;
        // Tipos en orden: id_campeon(i), nombre_build(s), popularidad(i), primary_path(i),
        // secondary_path(i), win_rate(d), total_matches(i)
        $stmt->bind_param("isiiidi", $id_campeon, $nombre_build, $popularidad, $primary_path, $secondary_path, $win_rate, $total_matches);
        if (!$stmt->execute()) return 0;
        return (int)$this->_db->insert_id;
    }

    public function add_build_objeto(int $id_build, int $id_objeto, int $orden, string $fase): bool
    {
        $stmt = $this->_db->prepare("INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES (?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("iiis", $id_build, $id_objeto, $orden, $fase);
        return $stmt->execute();
    }

    public function add_build_runa(int $id_build, int $id_runa): bool
    {
        $stmt = $this->_db->prepare("INSERT INTO build_runa (id_build, id_runa) VALUES (?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("ii", $id_build, $id_runa);
        return $stmt->execute();
    }

    public function get_objetos_admin(): array
    {
        $res = $this->_db->query("SELECT id, nombre FROM objeto ORDER BY nombre");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function get_runas_admin(): array
    {
        $res = $this->_db->query("SELECT id, nombre, tipo FROM runa ORDER BY tipo, nombre");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function get_runas_por_camino(): array
    {
        // Detectar si existe la columna path_id (migración aplicada)
        $has_path = false;
        $cols = $this->_db->query("SHOW COLUMNS FROM runa LIKE 'path_id'");
        if ($cols && $cols->num_rows > 0) $has_path = true;

        $sql = $has_path
            ? "SELECT id, nombre, path_id, tipo FROM runa ORDER BY nombre"
            : "SELECT id, nombre, NULL AS path_id, tipo FROM runa ORDER BY nombre";

        $res = $this->_db->query($sql);
        if (!$res) return [];
        $por_camino = [];
        while ($r = $res->fetch_assoc()) {
            $pid = intval($r['path_id'] ?? 0);
            $por_camino[$pid][] = [
                'id'     => intval($r['id']),
                'nombre' => $r['nombre'],
                'tipo'   => $r['tipo']
            ];
        }
        return $por_camino;
    }

    public function get_campeones(): array
    {
        $res = $this->_db->query("SELECT id, nombre, rol, foto FROM campeon ORDER BY nombre ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function delete_champion(int $id): array
    {
        $this->_db->begin_transaction();
        try {
            // Obtener builds del campeón
            $stmt = $this->_db->prepare("SELECT id FROM build WHERE id_campeon = ?");
            if (!$stmt) throw new Exception('Error preparando SELECT build.');
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $builds = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            foreach ($builds as $build) {
                $id_build = $build['id'];

                $s1 = $this->_db->prepare("DELETE FROM build_runa WHERE id_build = ?");
                if (!$s1) throw new Exception('Error preparando DELETE build_runa.');
                $s1->bind_param("i", $id_build);
                if (!$s1->execute()) throw new Exception('Error eliminando build_runa.');
                $s1->close();

                $s2 = $this->_db->prepare("DELETE FROM build_objeto WHERE id_build = ?");
                if (!$s2) throw new Exception('Error preparando DELETE build_objeto.');
                $s2->bind_param("i", $id_build);
                if (!$s2->execute()) throw new Exception('Error eliminando build_objeto.');
                $s2->close();
            }

            $s3 = $this->_db->prepare("DELETE FROM build WHERE id_campeon = ?");
            if (!$s3) throw new Exception('Error preparando DELETE build.');
            $s3->bind_param("i", $id);
            if (!$s3->execute()) throw new Exception('Error eliminando builds.');
            $s3->close();

            $s4 = $this->_db->prepare("DELETE FROM campeon WHERE id = ?");
            if (!$s4) throw new Exception('Error preparando DELETE campeon.');
            $s4->bind_param("i", $id);
            if (!$s4->execute()) throw new Exception('Error eliminando campeón.');
            $s4->close();

            $this->_db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->_db->rollback();
            return ['success' => false, 'error' => 'Error al eliminar el campeón: ' . $e->getMessage()];
        }
    }

    public function get_build_by_campeon(int $id_campeon): ?array
    {
        $stmt = $this->_db->prepare("SELECT * FROM build WHERE id_campeon = ? LIMIT 1");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id_campeon);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$res || $res->num_rows === 0) { $stmt->close(); return null; }
        $build = $res->fetch_assoc();
        $stmt->close();

        $s2 = $this->_db->prepare(
            "SELECT bo.orden, bo.fase, bo.id_objeto, o.nombre
             FROM build_objeto bo
             JOIN objeto o ON o.id = bo.id_objeto
             WHERE bo.id_build = ?
             ORDER BY bo.orden ASC"
        );
        if ($s2) {
            $s2->bind_param("i", $build['id']);
            $s2->execute();
            $build['objetos'] = $s2->get_result()->fetch_all(MYSQLI_ASSOC);
            $s2->close();
        } else {
            $build['objetos'] = [];
        }

        // Detección defensiva de la columna path_id en runa (migración aplicada o no)
        $has_path = false;
        $cols = $this->_db->query("SHOW COLUMNS FROM runa LIKE 'path_id'");
        if ($cols && $cols->num_rows > 0) $has_path = true;

        $sql_runas = $has_path
            ? "SELECT br.id_runa, r.nombre, r.tipo, r.path_id
               FROM build_runa br
               JOIN runa r ON r.id = br.id_runa
               WHERE br.id_build = ?"
            : "SELECT br.id_runa, r.nombre, r.tipo, NULL AS path_id
               FROM build_runa br
               JOIN runa r ON r.id = br.id_runa
               WHERE br.id_build = ?";

        $s3 = $this->_db->prepare($sql_runas);
        if ($s3) {
            $s3->bind_param("i", $build['id']);
            $s3->execute();
            $build['runas'] = $s3->get_result()->fetch_all(MYSQLI_ASSOC);
            $s3->close();
        } else {
            $build['runas'] = [];
        }

        return $build;
    }

    public function crear_build_campeon(int $id_campeon, array $datos_build): bool
    {
        $nombre_build   = $datos_build['nombre_build']   ?? '';
        $popularidad    = intval($datos_build['popularidad']    ?? 80);
        $primary_path   = intval($datos_build['primary_path']   ?? 8000);
        $secondary_path = intval($datos_build['secondary_path'] ?? 8100);
        $win_rate       = floatval($datos_build['win_rate']     ?? 50.0);
        $total_matches  = intval($datos_build['total_matches']  ?? 10000);

        $id_build = $this->add_build($id_campeon, $nombre_build, $popularidad, $primary_path, $secondary_path, $win_rate, $total_matches);
        if (!$id_build) return false;

        $obj_ids   = $datos_build['obj_id']    ?? [];
        $obj_fases = $datos_build['obj_fase']  ?? [];
        $obj_orden = $datos_build['obj_orden'] ?? [];
        foreach ($obj_ids as $idx => $id_objeto) {
            $id_objeto = intval($id_objeto);
            if (!$id_objeto) continue;
            $fase  = in_array($obj_fases[$idx] ?? '', ['starter','early','core','full']) ? $obj_fases[$idx] : 'core';
            $orden = intval($obj_orden[$idx] ?? ($idx + 1));
            $this->add_build_objeto($id_build, $id_objeto, $orden, $fase);
        }

        $runa_ids = $datos_build['runa_id'] ?? [];
        foreach ($runa_ids as $id_runa) {
            $id_runa = intval($id_runa);
            if (!$id_runa) continue;
            $this->add_build_runa($id_build, $id_runa);
        }

        return true;
    }

    public function update_build_campeon(int $id_campeon, array $datos_build): bool
    {
        $stmt = $this->_db->prepare("SELECT id FROM build WHERE id_campeon = ? LIMIT 1");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id_campeon);
        $stmt->execute();
        $res      = $stmt->get_result();
        $existing = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
        $stmt->close();

        $nombre_build   = $datos_build['nombre_build']   ?? '';
        $popularidad    = intval($datos_build['popularidad']    ?? 80);
        $primary_path   = intval($datos_build['primary_path']   ?? 8000);
        $secondary_path = intval($datos_build['secondary_path'] ?? 8100);
        $win_rate       = floatval($datos_build['win_rate']     ?? 50.0);
        $total_matches  = intval($datos_build['total_matches']  ?? 10000);

        if ($existing) {
            $id_build = $existing['id'];
            $su = $this->_db->prepare(
                "UPDATE build SET nombre_build=?, popularidad=?, primary_path=?, secondary_path=?, win_rate=?, total_matches=? WHERE id=?"
            );
            if (!$su) return false;
            $su->bind_param("siiidii", $nombre_build, $popularidad, $primary_path, $secondary_path, $win_rate, $total_matches, $id_build);
            if (!$su->execute()) { $su->close(); return false; }
            $su->close();

            $d1 = $this->_db->prepare("DELETE FROM build_objeto WHERE id_build = ?");
            if ($d1) { $d1->bind_param("i", $id_build); $d1->execute(); $d1->close(); }
            $d2 = $this->_db->prepare("DELETE FROM build_runa WHERE id_build = ?");
            if ($d2) { $d2->bind_param("i", $id_build); $d2->execute(); $d2->close(); }
        } else {
            $id_build = $this->add_build($id_campeon, $nombre_build, $popularidad, $primary_path, $secondary_path, $win_rate, $total_matches);
            if (!$id_build) return false;
        }

        $obj_ids   = $datos_build['obj_id']    ?? [];
        $obj_fases = $datos_build['obj_fase']  ?? [];
        $obj_orden = $datos_build['obj_orden'] ?? [];
        foreach ($obj_ids as $idx => $id_objeto) {
            $id_objeto = intval($id_objeto);
            if (!$id_objeto) continue;
            $fase  = in_array($obj_fases[$idx] ?? '', ['starter','early','core','full']) ? $obj_fases[$idx] : 'core';
            $orden = intval($obj_orden[$idx] ?? ($idx + 1));
            $this->add_build_objeto($id_build, $id_objeto, $orden, $fase);
        }

        $runa_ids = $datos_build['runa_id'] ?? [];
        foreach ($runa_ids as $id_runa) {
            $id_runa = intval($id_runa);
            if (!$id_runa) continue;
            $this->add_build_runa($id_build, $id_runa);
        }

        return true;
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

    public function get_ligas(): array
    {
        $res = $this->_db->query("SELECT id, nombre FROM liga ORDER BY nombre ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function get_equipos_by_liga(int $id_liga): array
    {
        $stmt = $this->_db->prepare("SELECT id, nombre FROM equipo WHERE id_liga = ? ORDER BY nombre");
        if (!$stmt) return [];
        $stmt->bind_param("i", $id_liga);
        $stmt->execute();
        $res = $stmt->get_result();
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

    public function get_todos_equipos_admin(): array
    {
        $res = $this->_db->query(
            "SELECT e.id, e.nombre, l.nombre AS nombre_liga, e.ranking
             FROM equipo e
             LEFT JOIN liga l ON l.id = e.id_liga
             ORDER BY e.ranking ASC, e.nombre ASC"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function add_equipo(string $nombre, int $id_liga, string $logo, string $descripcion, int $ranking, string $video): int
    {
        $stmt = $this->_db->prepare(
            "INSERT INTO equipo (nombre, id_liga, Logo, descripcion, ranking, video_highlights) VALUES (?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return 0;
        $stmt->bind_param("sissis", $nombre, $id_liga, $logo, $descripcion, $ranking, $video);
        if (!$stmt->execute()) return 0;
        return (int)$this->_db->insert_id;
    }

    public function get_roster_by_equipo(int $id_equipo): array
    {
        $stmt = $this->_db->prepare(
            "SELECT id, nombre, nick, posicion, foto_url
             FROM roster
             WHERE id_equipo = ? AND activo = 1
             ORDER BY FIELD(posicion, 'Top','Jungla','Mid','ADC','Support','Coach')"
        );
        if (!$stmt) return [];
        $stmt->bind_param("i", $id_equipo);
        $stmt->execute();
        $res  = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $por_pos = [];
        foreach ($rows as $r) {
            $por_pos[$r['posicion']] = $r;
        }
        return $por_pos;
    }

    public function update_roster_equipo(int $id_equipo, array $roster_data): bool
    {
        $posiciones = ['Top','Jungla','Mid','ADC','Support','Coach'];
        foreach ($posiciones as $pos) {
            $entry = $roster_data[$pos] ?? null;
            if (!is_array($entry)) continue;
            $nombre    = trim($entry['nombre']    ?? '');
            $nick      = trim($entry['nick']      ?? '');
            $foto_url  = trim($entry['foto_url']  ?? '');
            $roster_id = intval($entry['roster_id'] ?? 0);
            $vacio     = ($nombre === '' && $nick === '');

            if ($vacio) {
                if ($roster_id > 0) {
                    $s = $this->_db->prepare("UPDATE roster SET activo = 0 WHERE id = ?");
                    if (!$s) return false;
                    $s->bind_param("i", $roster_id);
                    $s->execute();
                    $s->close();
                }
                continue;
            }

            if ($roster_id > 0) {
                $s = $this->_db->prepare(
                    "UPDATE roster SET nombre = ?, nick = ?, foto_url = ?, activo = 1 WHERE id = ?"
                );
                if (!$s) return false;
                $s->bind_param("sssi", $nombre, $nick, $foto_url, $roster_id);
                $s->execute();
                $s->close();
            } else {
                $s = $this->_db->prepare(
                    "INSERT INTO roster (id_equipo, nombre, nick, posicion, foto_url, activo)
                     VALUES (?, ?, ?, ?, ?, 1)"
                );
                if (!$s) return false;
                $s->bind_param("issss", $id_equipo, $nombre, $nick, $pos, $foto_url);
                $s->execute();
                $s->close();
            }
        }
        return true;
    }

    public function add_roster_member(int $id_equipo, string $nombre, string $nick, string $posicion): bool
    {
        $valid = ['Top','Jungla','Mid','ADC','Support','Coach'];
        if (!in_array($posicion, $valid)) return false;
        $stmt = $this->_db->prepare(
            "INSERT INTO roster (id_equipo, nombre, nick, posicion, activo) VALUES (?, ?, ?, ?, 1)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("isss", $id_equipo, $nombre, $nick, $posicion);
        return $stmt->execute();
    }

    public function get_campeon_by_id(int $id): array
    {
        $stmt = $this->_db->prepare("SELECT * FROM campeon WHERE id = ?");
        if (!$stmt) return [];
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res && $res->num_rows ? $res->fetch_assoc() : [];
    }

    public function update_campeon(int $id, string $nombre, string $rol, string $desc, string $foto, string $q, string $w, string $e, string $r): bool
    {
        $stmt = $this->_db->prepare(
            "UPDATE campeon SET nombre=?, rol=?, descripcion=?, foto=?, q=?, w=?, e=?, r=? WHERE id=?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("ssssssssi", $nombre, $rol, $desc, $foto, $q, $w, $e, $r, $id);
        return $stmt->execute();
    }

    public function get_equipo_by_id(int $id): array
    {
        $stmt = $this->_db->prepare(
            "SELECT e.*, l.nombre AS nombre_liga FROM equipo e LEFT JOIN liga l ON l.id = e.id_liga WHERE e.id = ?"
        );
        if (!$stmt) return [];
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res && $res->num_rows ? $res->fetch_assoc() : [];
    }

    public function update_equipo(int $id, string $nombre, int $id_liga, string $logo, string $desc, int $ranking, string $video): bool
    {
        $stmt = $this->_db->prepare(
            "UPDATE equipo SET nombre=?, id_liga=?, Logo=?, descripcion=?, ranking=?, video_highlights=? WHERE id=?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("sissisi", $nombre, $id_liga, $logo, $desc, $ranking, $video, $id);
        return $stmt->execute();
    }

    public function get_partido_by_id_admin(int $id): array
    {
        $stmt = $this->_db->prepare(
            "SELECT p.id, p.fecha, p.stream_url,
                j1.id_equipo AS id_equipo1, j2.id_equipo AS id_equipo2,
                j1.resultado AS res1, j2.resultado AS res2
             FROM partido p
             JOIN juega j1 ON j1.id_partido = p.id
             JOIN juega j2 ON j2.id_partido = p.id
             WHERE p.id = ? AND j1.id_equipo < j2.id_equipo"
        );
        if (!$stmt) return [];
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res && $res->num_rows ? $res->fetch_assoc() : [];
    }

    public function update_partido(int $id, string $fecha, int $id_eq1, int $id_eq2, int $res1, int $res2, string $stream_url = ''): bool
    {
        $stmt = $this->_db->prepare("UPDATE partido SET fecha=?, stream_url=? WHERE id=?");
        if (!$stmt) return false;
        $stmt->bind_param("ssi", $fecha, $stream_url, $id);
        if (!$stmt->execute()) return false;
        $stmt->close();

        $stmt2 = $this->_db->prepare("DELETE FROM juega WHERE id_partido=?");
        if (!$stmt2) return false;
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $stmt2->close();

        $stmt3 = $this->_db->prepare("INSERT INTO juega (id_partido, id_equipo, resultado) VALUES (?,?,?)");
        if (!$stmt3) return false;
        $stmt3->bind_param("iii", $id, $id_eq1, $res1);
        if (!$stmt3->execute()) return false;
        $stmt3->close();

        $stmt4 = $this->_db->prepare("INSERT INTO juega (id_partido, id_equipo, resultado) VALUES (?,?,?)");
        if (!$stmt4) return false;
        $stmt4->bind_param("iii", $id, $id_eq2, $res2);
        $ok = $stmt4->execute();
        $stmt4->close();
        return $ok;
    }

    public function delete_equipo(int $id): array
    {
        $stmt = $this->_db->prepare("SELECT COUNT(*) AS c FROM juega WHERE id_equipo = ?");
        if (!$stmt) return ['success' => false, 'error' => 'Error de preparación.'];
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['c'];
        $stmt->close();

        if ($count > 0) {
            return ['success' => false, 'error' => 'El equipo tiene partidos asociados. Elimina los partidos primero.'];
        }

        $stmt2 = $this->_db->prepare("DELETE FROM roster WHERE id_equipo = ?");
        if (!$stmt2) return ['success' => false, 'error' => 'Error al eliminar roster.'];
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $stmt2->close();

        $stmt3 = $this->_db->prepare("DELETE FROM equipo WHERE id = ?");
        if (!$stmt3) return ['success' => false, 'error' => 'Error al eliminar equipo.'];
        $stmt3->bind_param("i", $id);
        return $stmt3->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Error al eliminar el equipo.'];
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
