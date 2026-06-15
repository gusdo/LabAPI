<?php

require_once "Modelo/conexion.php";

$db = new DB();

$cn = $db->conectar();

echo "Conexión exitosa";

?>