<?php
require_once("MODELOS/modelo_publicacion.php");
    $modelo_publicacion = new modelo_publicacion;
    $ultimos_juegos = $modelo_publicacion->publicacion_aleatoria();
?>