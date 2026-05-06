<?php
    class conectar {
        public static function conexion(){
            $conexion = new mysqli("localhost", "root", "", "bd_final");
            $conexion-> set_charset("utf8");
            return $conexion;
            }
            public static function desconectar($conexion){
                $conexion->close();
            }
    }
?>