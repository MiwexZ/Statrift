<?php
require_once("../MODELOS/modelo_campeones.php");
    $modelo_campeon = new modelo_campeon;
    $ultimos_juegos = $modelo_campeon->mostrar_todos_campeones();
?>