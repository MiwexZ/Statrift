<?php 
    require_once("MODELOS/modelo_partido.php");
    $modelo_partido = new modelo_partido;
    $modelo_partido->mostrar_partidos_semana_actual();
?>