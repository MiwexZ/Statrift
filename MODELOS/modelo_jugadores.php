<?php
class modelo_jugadores
{
    private $_db;

    public function __construct()
    {
        $this->_db = conectar::conexion();
    }

    public function mostrar_todos_jugadores(): void
    {
        $query = "SELECT j.*, e.nombre AS nombre_equipo 
                  FROM jugador j 
                  LEFT JOIN equipo e ON j.id_equipo_favorito = e.id 
                  WHERE j.activo = 1
                  ORDER BY j.nombre ASC";
        $res = $this->_db->query($query);
        if (!$res) return;

        $roles = [
            'Administrador' => ['icon' => 'fa-shield-halved', 'color' => '#e5b962'],
            'Jugador'       => ['icon' => 'fa-gamepad',       'color' => '#94a3b8'],
        ];

        echo "<div class='row justify-content-center'>";
        $i = 0;
        while ($fila = $res->fetch_array(MYSQLI_ASSOC)) {
            $nombre   = htmlspecialchars($fila['nombre']   ?? '');
            $apellido = htmlspecialchars($fila['apellidos'] ?? '');
            $nick     = htmlspecialchars($fila['nick']     ?? '');
            $correo   = htmlspecialchars($fila['correo']   ?? '');
            $tipo     = htmlspecialchars($fila['tipo']     ?? 'Jugador');
            $equipo   = htmlspecialchars($fila['nombre_equipo'] ?? '');

            $role_info = $roles[$tipo] ?? $roles['Jugador'];
            $delay = ($i % 3) * 100;

            $equipo_badge = $equipo
                ? "<div class='player-team-badge mt-3'>
                       <i class='fa-solid fa-shield-halved me-1'></i> {$equipo}
                   </div>"
                : '';

            echo "
            <div class='col-12 col-md-6 col-lg-4 mb-4' data-aos='fade-up' data-aos-delay='{$delay}'>
                <div class='player-card bg-glass h-100'>
                    <div class='player-avatar'>
                        <i class='fa-solid fa-user-astronaut'></i>
                    </div>
                    <div class='player-info'>
                        <h4 class='player-name'>{$nombre} {$apellido}</h4>
                        <p class='player-nick'>@{$nick}</p>
                        <div class='player-role' style='color:{$role_info['color']}'>
                            <i class='fa-solid {$role_info['icon']} me-1'></i>{$tipo}
                        </div>
                        <p class='player-email mt-2'>
                            <i class='fa-solid fa-envelope text-gold me-2'></i>{$correo}
                        </p>
                        {$equipo_badge}
                    </div>
                </div>
            </div>";
            $i++;
        }
        echo "</div>";
    }

    public function generar_login(): void
    {
        $query = "SELECT foto FROM campeon ORDER BY RAND() LIMIT 1";
        $res   = $this->_db->query($query);
        $foto  = ($res && $fila = $res->fetch_assoc()) ? htmlspecialchars($fila['foto']) : '';

        echo "
        <section class='blanco fondo_login'>
            <div class='container-fluid'>
                <div class='row'>
                    <div class='vh-100 d-flex col-sm-12 text-black flex-column justify-content-center align-items-center'>
                        <div class='login-box bg-glass p-5 rounded-4' style='width:100%;max-width:420px;'>
                            <div class='text-center mb-4'>
                                <i class='fa-solid fa-dragon text-gold' style='font-size:2.5rem'></i>
                                <h2 class='mt-3 text-gold'>Bienvenido</h2>
                                <p class='text-muted'>Accede a tu cuenta de StatRift</p>
                            </div>
                            ";
                            if (isset($_GET['error']) && $_GET['error'] == 1) {
                                echo "<div class='alert alert-danger text-center shadow-sm mb-4' role='alert' style='border-left: 4px solid #dc3545;'>
                                        <i class='fa-solid fa-circle-exclamation me-2'></i> <strong>Error:</strong> Usuario o contraseña incorrectos.
                                      </div>";
                            }
                            if (isset($_GET['error']) && $_GET['error'] == 2) {
                                echo "<div class='alert alert-warning text-center shadow-sm mb-4' role='alert' style='border-left: 4px solid #ffc107;'>
                                        <i class='fa-solid fa-user-lock me-2'></i> Tu cuenta está inactiva. Contacta con soporte.
                                      </div>";
                            }
                            echo "
                            <form method='POST' action='../CONTROLADORES/iniciar_sesion.php'>
                                <div class='mb-3'>
                                    <label class='form-label text-muted' for='nick'><i class='fa-solid fa-user me-1'></i> Usuario</label>
                                    <input type='text' name='nick' id='nick' class='form-control' placeholder='Tu nick...' required>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label text-muted' for='pass'><i class='fa-solid fa-lock me-1'></i> Contraseña</label>
                                    <input type='password' name='pass' id='pass' class='form-control' placeholder='Tu contraseña...' required>
                                </div>
                                <div class='mb-4'>
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' name='cookie' id='cookie'>
                                        <label class='form-check-label text-muted' for='cookie'>Mantener sesión iniciada</label>
                                    </div>
                                </div>
                                <button type='submit' class='btn btn-gold w-100 py-2'>
                                    <i class='fa-solid fa-right-to-bracket me-2'></i>Entrar
                                </button>
                                <p class='text-center mt-3 mb-0'>
                                    <a class='text-muted small' href='/index.php'>
                                        <i class='fa-solid fa-arrow-left me-1'></i>Volver al inicio
                                    </a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>";
    }

    public function iniciar_sesion(string $nick, string $pass): void
    {
        $preparada = $this->_db->prepare("SELECT * FROM jugador WHERE nick = ?");
        $preparada->bind_param("s", $nick);
        $preparada->execute();
        $resultado = $preparada->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            if (password_verify($pass, $fila['pass'])) {
                if ($fila['activo'] == 1) {
                    session_start();
                    $_SESSION['nick'] = $nick;
                    if (isset($_POST['cookie'])) {
                        setcookie('usuario', $nick, time() + 60 * 60 * 24 * 365, '/');
                    }
                    if ($fila['tipo'] === 'Administrador') {
                        header("Location: ../VISTAS/gestion_admin.php");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit;
                } else {
                    header("Location: ../VISTAS/login.php?error=2");
                    exit;
                }
            } else {
                header("Location: ../VISTAS/login.php?error=1");
                exit;
            }
        } else {
            header("Location: ../VISTAS/login.php?error=1");
            exit;
        }
    }

    public function cerrar_sesion(): void
    {
        session_start();
        session_destroy();
        $_SESSION = [];
        if (isset($_COOKIE['usuario'])) {
            setcookie('usuario', '', time() - 60 * 60 * 24 * 365, '/');
        }
        header("Location: ../index.php");
        exit;
    }
}
?>