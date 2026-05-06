<?php
    require_once("../MODELOS/modelo_jugadores.php");
    $modelo_jugador = new modelo_jugadores;
    $modelo_jugador->generar_login();
?>