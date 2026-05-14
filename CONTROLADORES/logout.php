<?php
    ob_start();
    require_once("../BD/conectar_bd.php");
    require_once("../MODELOS/modelo_jugadores.php");
    $modelo_jugadores = new modelo_jugadores();
    $modelo_jugadores->cerrar_sesion();
?>