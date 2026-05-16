<?php
require_once __DIR__ . '/../MODELOS/modelo_equipo.php';
$modelo_equipo = new modelo_equipo();
$modelo_equipo->mostrar_todos_equipos();
?>
