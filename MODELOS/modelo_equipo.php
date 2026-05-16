<?php
class modelo_equipo
{
    private $_db;

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    public function get_todos_equipos(): array
    {
        $sql = "SELECT e.*, l.nombre AS nombre_liga
                FROM equipo e
                LEFT JOIN liga l ON e.id_liga = l.id
                ORDER BY e.ranking ASC, e.nombre ASC";
        $res = $this->_db->query($sql);
        if (!$res) return [];
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function get_equipo_info(int $id): ?array
    {
        $stmt = $this->_db->prepare(
            "SELECT e.*, l.nombre AS nombre_liga
             FROM equipo e
             LEFT JOIN liga l ON e.id_liga = l.id
             WHERE e.id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function get_roster(int $id_equipo): array
    {
        $stmt = $this->_db->prepare(
            "SELECT * FROM roster
             WHERE id_equipo = ? AND activo = 1
             ORDER BY FIELD(posicion,'Top','Jungla','Mid','ADC','Support','Coach')"
        );
        $stmt->bind_param("i", $id_equipo);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function mostrar_todos_equipos(): void
    {
        $equipos = $this->get_todos_equipos();

        if (empty($equipos)) {
            echo "<div class='text-center text-muted py-5'><i class='fa-solid fa-ghost fs-1 mb-3 d-block'></i>No hay equipos registrados.</div>";
            return;
        }

        $pos_icons = [
            'Top'     => ['icon' => 'fa-shield-halved',      'color' => '#3b82f6'],
            'Jungla'  => ['icon' => 'fa-tree',               'color' => '#22c55e'],
            'Mid'     => ['icon' => 'fa-star',               'color' => '#a855f7'],
            'ADC'     => ['icon' => 'fa-crosshairs',         'color' => '#ef4444'],
            'Support' => ['icon' => 'fa-hand-holding-heart', 'color' => '#e5b962'],
            'Coach'   => ['icon' => 'fa-clipboard',          'color' => '#94a3b8'],
        ];

        echo "<div class='row justify-content-center'>";
        foreach ($equipos as $i => $e) {
            $id     = intval($e['id']);
            $nombre = htmlspecialchars($e['nombre'] ?? '');
            $liga   = htmlspecialchars($e['nombre_liga'] ?? '');
            $logo   = htmlspecialchars($e['Logo'] ?? '');
            $rank   = intval($e['ranking']);
            $delay  = ($i % 4) * 100;

            $logo_html = $logo
                ? "<img src='{$logo}' alt='{$nombre}' class='team-card-logo' loading='lazy' onerror=\"this.style.display='none'\">"
                : '';

            $rank_badge = $rank > 0
                ? "<span class='team-rank-badge'>GPR #<strong>{$rank}</strong></span>"
                : '';

            $liga_badge = $liga
                ? "<small class='text-gold d-block mt-1'><i class='fa-solid fa-trophy me-1'></i>{$liga}</small>"
                : '';

            echo "
            <div class='col-12 col-sm-6 col-lg-4 col-xl-3 mb-4' data-aos='fade-up' data-aos-delay='{$delay}'>
                <a href='/VISTAS/ver_equipo.php?id={$id}' class='text-decoration-none'>
                    <div class='team-card bg-glass h-100 text-center'>
                        <div class='team-card-logo-wrap'>
                            {$logo_html}
                        </div>
                        <div class='team-card-body'>
                            {$rank_badge}
                            <h4 class='team-card-name'>{$nombre}</h4>
                            {$liga_badge}
                        </div>
                    </div>
                </a>
            </div>";
        }
        echo "</div>";
    }
}
?>
