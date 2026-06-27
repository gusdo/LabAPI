<?php

class Conexion{

    private $host = "localhost";
    private $dbname = "productosdb";
    private $user = "root";
    private $pass = "";

    public function conectar(){

        try{

            $pdo = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->dbname,
                $this->user,
                $this->pass
            );

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $pdo;

        }catch(PDOException $e){

            die("Error de conexión: " . $e->getMessage());

        }
    }
}

?>