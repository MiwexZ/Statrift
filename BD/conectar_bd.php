<?php
    class conectar {
        public static function conexion(){
            $conexion = new mysqli("db", "miwex", "10fUETl9gnuDoJfR1Mtc", "statrift_db");
            $conexion-> set_charset("utf8mb4");
            return $conexion;
            }
            public static function desconectar($conexion){
                $conexion->close();
            }
    }
?>