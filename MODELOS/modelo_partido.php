<?php
class modelo_partido
{
    private $_db;
    private $partido;
    public function __construct()
    {
        $this->_db = conectar::conexion();
        $this->partido = array();
    }
    function mostrar_partidos_semana_actual() {
        $fecha_inicio = date('Y-m-d', strtotime('monday this week'));
        $fecha_fin    = date('Y-m-d', strtotime('sunday this week'));

        $stmt = $this->_db->prepare(
            "SELECT p.id AS id_partido, p.fecha, e1.nombre AS equipo1, e2.nombre AS equipo2
             FROM partido p
             JOIN juega j1 ON p.id = j1.id_partido
             JOIN equipo e1 ON j1.id_equipo = e1.id
             JOIN juega j2 ON p.id = j2.id_partido AND j1.id_equipo < j2.id_equipo
             JOIN equipo e2 ON j2.id_equipo = e2.id
             WHERE p.fecha BETWEEN ? AND ?
             ORDER BY p.fecha ASC"
        );
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $resultado = $stmt->get_result();

        echo '<h2 class="section-title text-center mt-5 mb-4 d-block">Partidos de la Semana</h2>';
        echo '<div class="table-responsive w-100 mx-auto p-4 rounded-4 bg-glass partidos-widget mb-5" style="max-width: 800px;">';
        echo '<table class="table table-borderless align-middle text-center mb-0">';
        echo '<thead>';
        echo '<tr>
                <th style="width:1%;white-space:nowrap">Fecha</th>
                <th class="text-end">Equipo 1</th>
                <th style="width:1%;white-space:nowrap"></th>
                <th class="text-start">Equipo 2</th>
              </tr>';
        echo '</thead>';
        echo '<tbody>';

        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $equipo1    = htmlspecialchars($fila['equipo1']);
                $equipo2    = htmlspecialchars($fila['equipo2']);
                $fecha      = date('d/m/Y', strtotime($fila['fecha']));
                $id_partido = intval($fila['id_partido']);
                echo '<tr class="partido-row" onclick="location.href=\'/VISTAS/ver_partido.php?id=' . $id_partido . '\'" title="Ver partido">';
                echo '<td style="white-space:nowrap"><span class="text-gold small fw-bold"><i class="fa-regular fa-calendar-alt me-1"></i>' . $fecha . '</span></td>';
                echo '<td class="text-end"><strong class="text-light">' . $equipo1 . '</strong></td>';
                echo '<td class="px-2"><span class="badge bg-danger rounded-pill px-2 shadow-sm">VS</span></td>';
                echo '<td class="text-start"><strong class="text-light">' . $equipo2 . '</strong></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4" class="text-muted py-4"><i class="fa-solid fa-calendar-xmark mb-2 fs-3 d-block"></i> No hay partidos programados para esta semana.</td></tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    public function get_partidos_por_liga(int $id_liga, int $meses = 2): array
    {
        $stmt = $this->_db->prepare(
            "SELECT p.id, p.fecha,
                    e1.nombre AS equipo1, e1.Logo AS logo1,
                    e2.nombre AS equipo2, e2.Logo AS logo2
             FROM partido p
             JOIN juega j1 ON p.id = j1.id_partido
             JOIN equipo e1 ON j1.id_equipo = e1.id
             JOIN juega j2 ON p.id = j2.id_partido AND j1.id_equipo < j2.id_equipo
             JOIN equipo e2 ON j2.id_equipo = e2.id
             WHERE e1.id_liga = ?
               AND p.fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? MONTH)
             ORDER BY p.fecha ASC"
        );
        $stmt->bind_param("ii", $id_liga, $meses);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function get_partido_detalle(int $id_partido): ?array
    {
        $stmt = $this->_db->prepare(
            "SELECT p.id, p.fecha, p.stream_url,
                    e1.id AS id_equipo1, e1.nombre AS equipo1, e1.Logo AS logo1, e1.id_liga,
                    e2.id AS id_equipo2, e2.nombre AS equipo2, e2.Logo AS logo2,
                    j1.resultado AS res1, j2.resultado AS res2
             FROM partido p
             JOIN juega j1 ON p.id = j1.id_partido
             JOIN equipo e1 ON j1.id_equipo = e1.id
             JOIN juega j2 ON p.id = j2.id_partido AND j1.id_equipo < j2.id_equipo
             JOIN equipo e2 ON j2.id_equipo = e2.id
             WHERE p.id = ?"
        );
        $stmt->bind_param("i", $id_partido);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>