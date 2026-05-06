<?php
    class modelo_campeon
    {
        private $_db;
        private $comentario;
        public function __construct()
        {
            $this->_db = conectar::conexion();
            $this->comentario = array();
        }

        function mostrar_coment(){
            $conexion = conectar::conexion();
            $query = "SELECT * FROM comentario";

            $resultado = $this->_db->query($query);
            if (!$resultado){
                echo "Error en la consulta ";
            }else {
                while ($fila = $resultado->fetch_array(MYSQLI_ASSOC)){
                    echo"
                    <div class='d-flex justify-content-center'>
                        
                    ";
                }
            }
        }
    }
?>