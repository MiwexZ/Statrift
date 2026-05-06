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
            <a class='navbar-brand' href='#'><i class='fa-solid fa-dragon'></i> StatRift</a>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarnav'
                aria-controls='navbarnav' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarnav'>
                <ul class='navbar-nav'>
                    <li class='nav-item'>
                        <a class='nav-link' href='/statrift/index.php'>Inicio</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/statrift/VISTAS/jugadores.php'>Jugadores</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/statrift/VISTAS/campeones.php'>Campeones</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/statrift/VISTAS/Gestion Ligas.php'>Ligas</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link text-gold' href='/statrift/VISTAS/comunidad.php'><i class='fa-solid fa-users'></i> Comunidad</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link text-danger fw-bold' href='/statrift/VISTAS/gestion_admin.php'><i class='fa-solid fa-screwdriver-wrench'></i> Panel Admin</a>
                    </li>
                    <li class='nav-item active'>
                        <a class='nav-link text-gold' href='/statrift/CONTROLADORES/logout.php'>Cerrar sesión (".$_SESSION['nick'].")</a>
                    </li>
                </ul>
                <!-- barra de búsqueda -->
                <form class='d-flex ms-auto my-2 my-lg-0'>
                    <input class='form-control me-sm-2' type='search' placeholder='Buscar...' aria-label='Search'>
                    <button class='btn btn-outline-success my-2 my-sm-0' type='submit'><i class='fa-solid fa-search'></i></button>
                </form>
            </div>
        </div>
    </nav>
        ";
        }else{
            echo"
            <nav class='navbar navbar-expand-lg sticky-top'>
            <div class='container'>
                <a class='navbar-brand' href='#'><i class='fa-solid fa-dragon'></i> StatRift</a>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarnav'
                    aria-controls='navbarnav' aria-expanded='false' aria-label='Toggle navigation'>
                    <span class='navbar-toggler-icon'></span>
                </button>
                <div class='collapse navbar-collapse' id='navbarnav'>
                    <ul class='navbar-nav'>
                        <li class='nav-item'>
                            <a class='nav-link' href='/statrift/index.php'>Inicio</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/statrift/VISTAS/jugadores.php'>Jugadores</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/statrift/VISTAS/campeones.php'>Campeones</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='/statrift/VISTAS/Ligas.php'>Ligas</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link text-gold' href='/statrift/VISTAS/comunidad.php'><i class='fa-solid fa-users'></i> Comunidad</a>
                        </li>
                        <li class='nav-item dropdown'>
                            <a class='nav-link text-gold dropdown-toggle' href='#' id='navbarDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                <i class='fa-solid fa-user me-1'></i> ".$_SESSION['nick']."
                            </a>
                            <ul class='dropdown-menu dropdown-menu-dark border-secondary' aria-labelledby='navbarDropdown'>
                                <li><a class='dropdown-item' href='/statrift/VISTAS/perfil.php'><i class='fa-solid fa-id-card me-2'></i>Mi Perfil</a></li>
                                <li><hr class='dropdown-divider border-secondary'></li>
                                <li><a class='dropdown-item text-danger' href='/statrift/CONTROLADORES/logout.php'><i class='fa-solid fa-power-off me-2'></i>Cerrar sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                    <!-- barra de búsqueda -->
                    <form class='d-flex ms-auto my-2 my-lg-0'>
                        <input class='form-control me-sm-2' type='search' placeholder='Buscar...' aria-label='Search'>
                        <button class='btn btn-outline-success my-2 my-sm-0' type='submit'><i class='fa-solid fa-search'></i></button>
                    </form>
                </div>
            </div>
        </nav>
            ";  
        }
    }else{
        echo"
        <nav class='navbar navbar-expand-lg sticky-top'>
        <div class='container'>
            <a class='navbar-brand' href='#'><i class='fa-solid fa-dragon'></i> StatRift</a>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarnav'
                aria-controls='navbarnav' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarnav'>
                <ul class='navbar-nav'>
                    <li class='nav-item'>
                        <a class='nav-link' href='/statrift/index.php'>Inicio</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/statrift/VISTAS/jugadores.php'>Jugadores</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/statrift/VISTAS/campeones.php'>Campeones</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='/statrift/VISTAS/ligas.php'>Ligas</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link text-gold' href='/statrift/VISTAS/comunidad.php'><i class='fa-solid fa-users'></i> Comunidad</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link text-gold' href='/statrift/VISTAS/login.php'><i class='fa-solid fa-user'></i> Acceso</a>
                    </li>
                </ul>
                <!-- barra de búsqueda -->
                <form class='d-flex ms-auto my-2 my-lg-0'>
                    <input class='form-control me-sm-2' type='search' placeholder='Buscar...' aria-label='Search'>
                    <button class='btn btn-outline-success my-2 my-sm-0' type='submit'><i class='fa-solid fa-search'></i></button>
                </form>
            </div>
        </div>
    </nav>
        ";
    }
  }
?>