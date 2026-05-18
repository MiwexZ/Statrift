<?php
function cambiar_formato_fecha($fecha)
{
    $fecha = explode("-", $fecha);
    $fecha = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];
    return $fecha;
}
function generar_nombre_aleatorio(){
    $nombre = "";
    $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $longitud = strlen($caracteres);
    for ($i = 0; $i < 10; $i++) {
        $nombre .= $caracteres[rand(0, $longitud - 1)];
    }
    return $nombre;
}
function generar_nombre_unico($extension) {
    $fecha = date('YmdHis');
    $numero_aleatorio = rand(0, 10000);
    return $fecha . "-" . $numero_aleatorio . "." . $extension;
  }
  function cifrar_pass($password)
  {
      return password_hash($password, PASSWORD_DEFAULT);
  }
  function generar_header(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if(isset($_SESSION['nick'])){
        if($_SESSION['nick']=="admin"){
            echo"
            <nav class='navbar navbar-expand-lg sticky-top'>
            <div class='container'>
                <a class='navbar-brand' href='/index.php'><i class='fa-solid fa-dragon'></i> StatRift</a>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarnav'
                    aria-controls='navbarnav' aria-expanded='false' aria-label='Toggle navigation'>
                    <span class='navbar-toggler-icon'></span>
                </button>
                <div class='collapse navbar-collapse' id='navbarnav'>
                    <ul class='navbar-nav'>
                        <li class='nav-item'>
                            <a class='nav-link' href='/index.php'>Inicio</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/VISTAS/equipos.php'>Equipos</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/VISTAS/campeones.php'>Campeones</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/VISTAS/ligas.php'>Ligas</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link text-gold' href='/VISTAS/comunidad.php'>Comunidad</a>
                        </li>
                        <li class='nav-item dropdown'>
                            <a class='nav-link text-danger fw-bold dropdown-toggle' href='#' id='navbarAdminDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                <i class='fa-solid fa-screwdriver-wrench me-1'></i> ".$_SESSION['nick']."
                            </a>
                            <ul class='dropdown-menu dropdown-menu-dark border-secondary' aria-labelledby='navbarAdminDropdown'>
                                <li><a class='dropdown-item' href='/VISTAS/gestion_admin.php'><i class='fa-solid fa-screwdriver-wrench me-2'></i>Panel Admin</a></li>
                                <li><hr class='dropdown-divider border-secondary'></li>
                                <li><a class='dropdown-item text-danger' href='/CONTROLADORES/logout.php'><i class='fa-solid fa-power-off me-2'></i>Cerrar sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
            ";
        }else{
            echo"
            <nav class='navbar navbar-expand-lg sticky-top'>
            <div class='container'>
                <a class='navbar-brand' href='/index.php'><i class='fa-solid fa-dragon'></i> StatRift</a>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarnav'
                    aria-controls='navbarnav' aria-expanded='false' aria-label='Toggle navigation'>
                    <span class='navbar-toggler-icon'></span>
                </button>
                <div class='collapse navbar-collapse' id='navbarnav'>
                    <ul class='navbar-nav'>
                        <li class='nav-item'>
                            <a class='nav-link' href='/index.php'>Inicio</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/VISTAS/equipos.php'>Equipos</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/VISTAS/campeones.php'>Campeones</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/VISTAS/ligas.php'>Ligas</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link text-gold' href='/VISTAS/comunidad.php'>Comunidad</a>
                        </li>
                        <li class='nav-item dropdown'>
                            <a class='nav-link text-gold dropdown-toggle' href='#' id='navbarDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                ".$_SESSION['nick']."
                            </a>
                            <ul class='dropdown-menu dropdown-menu-dark border-secondary' aria-labelledby='navbarDropdown'>
                                <li><a class='dropdown-item' href='/VISTAS/perfil.php'><i class='fa-solid fa-id-card me-2'></i>Mi Perfil</a></li>
                                <li><hr class='dropdown-divider border-secondary'></li>
                                <li><a class='dropdown-item text-danger' href='/CONTROLADORES/logout.php'><i class='fa-solid fa-power-off me-2'></i>Cerrar sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
            ";  
        }
    }else{
        echo"
        <nav class='navbar navbar-expand-lg sticky-top'>
        <div class='container'>
            <a class='navbar-brand' href='/index.php'><i class='fa-solid fa-dragon'></i> StatRift</a>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarnav'
                aria-controls='navbarnav' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarnav'>
                <ul class='navbar-nav'>
                    <li class='nav-item'>
                        <a class='nav-link' href='/index.php'>Inicio</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/VISTAS/equipos.php'>Equipos</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/VISTAS/campeones.php'>Campeones</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/VISTAS/ligas.php'>Ligas</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link text-gold' href='/VISTAS/comunidad.php'>Comunidad</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link text-gold' href='/VISTAS/login.php'>Acceso</a>
                    </li>
                    <li class='nav-item ms-1'>
                        <a href='/VISTAS/registro.php' class='btn btn-outline-warning btn-sm my-2 my-lg-0'>Registrarse</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
        ";
    }
  }
?>
