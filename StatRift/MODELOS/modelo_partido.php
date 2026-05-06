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
        // Obtener la fecha de inicio y fin de la semana actual
        $fecha_inicio = date('Y-m-d', strtotime('monday this week'));
        $fecha_fin = date('Y-m-d', strtotime('sunday this week'));
      
        // Consulta uniendo las tablas partido, juega y equipo
        $consulta = "SELECT p.fecha, e1.nombre AS equipo1, e2.nombre AS equipo2 
                     FROM partido p
                     JOIN juega j1 ON p.id = j1.id_partido
                     JOIN equipo e1 ON j1.id_equipo = e1.id
                     JOIN juega j2 ON p.id = j2.id_partido AND j1.id_equipo < j2.id_equipo
                     JOIN equipo e2 ON j2.id_equipo = e2.id
                     WHERE p.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                     ORDER BY p.fecha ASC";
        
        $resultado = $this->_db->query($consulta);
      
        // Mostrar los partidos en una tabla HTML
        echo '<h2 class="section-title text-center mt-5 mb-4 d-block">Partidos de la Semana</h2>';
        echo '<div class="table-responsive w-100 mx-auto p-4 rounded-4 bg-glass border-gold-subtle mb-5" style="max-width: 800px;">';
        echo '<table class="table table-dark table-hover table-borderless align-middle text-center mb-0" style="background: transparent;">';
        echo '<thead style="border-bottom: 2px solid rgba(197, 160, 89, 0.5); color: var(--gold-accent);">';
        echo '<tr><th>Fecha</th><th class="text-end">Equipo 1</th><th></th><th class="text-start">Equipo 2</th></tr>';
        echo '</thead>';
        echo '<tbody>';
        
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                echo '<tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">';
                echo '<td class="text-start"><span class="text-gold small fw-bold"><i class="fa-regular fa-calendar-alt me-1"></i> ' . date('d/m/Y', strtotime($fila['fecha'])) . '</span></td>';
                echo '<td class="text-end"><strong class="fs-5 text-light">' . $fila['equipo1'] . '</strong></td>';
                echo '<td><span class="badge bg-danger rounded-pill px-3 shadow-sm">VS</span></td>';
                echo '<td class="text-start"><strong class="fs-5 text-light">' . $fila['equipo2'] . '</strong></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4" class="text-muted py-4"><i class="fa-solid fa-calendar-xmark mb-2 fs-3 d-block"></i> No hay partidos programados para esta semana.</td></tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}
?>