<?php

require_once "conexion.php";

class Producto{

    private $codigo;
    private $producto;
    private $precio;
    private $cantidad;

    public function __construct(
        $codigo = "",
        $producto = "",
        $precio = 0,
        $cantidad = 0
    ){

        $this->codigo = $codigo;
        $this->producto = $producto;
        $this->precio = $precio;
        $this->cantidad = $cantidad;

    }

    public function guardar(){

        try{

            $db = new DB();
            $cn = $db->conectar();

            $sql = "INSERT INTO productos
                    (codigo, producto, precio, cantidad)
                    VALUES (?, ?, ?, ?)";

            $stmt = $cn->prepare($sql);

            return $stmt->execute([
                $this->codigo,
                $this->producto,
                $this->precio,
                $this->cantidad
            ]);

        }catch(Exception $e){

            return false;

        }
    }

    public function editar($id){

        try{

            $db = new DB();
            $cn = $db->conectar();

            $sql = "UPDATE productos
                    SET codigo=?,
                        producto=?,
                        precio=?,
                        cantidad=?
                    WHERE id=?";

            $stmt = $cn->prepare($sql);

            return $stmt->execute([
                $this->codigo,
                $this->producto,
                $this->precio,
                $this->cantidad,
                $id
            ]);

        }catch(Exception $e){

            return false;

        }
    }

    public static function eliminar($id){

        try{

            $db = new DB();
            $cn = $db->conectar();

            $stmt = $cn->prepare(
                "DELETE FROM productos WHERE id=?"
            );

            return $stmt->execute([$id]);

        }catch(Exception $e){

            return false;

        }
    }

    public static function buscar($codigo){

        $db = new DB();
        $cn = $db->conectar();

        $stmt = $cn->prepare(
            "SELECT * FROM productos
             WHERE codigo=?"
        );

        $stmt->execute([$codigo]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function listar(){

        $db = new DB();
        $cn = $db->conectar();

        return $cn->query(
            "SELECT * FROM productos"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function existeCodigo($codigo){

        $db = new DB();
        $cn = $db->conectar();

        $stmt = $cn->prepare(
            "SELECT COUNT(*) total
             FROM productos
             WHERE codigo=?"
        );

        $stmt->execute([$codigo]);

        return $stmt->fetch(
            PDO::FETCH_ASSOC
        )["total"] > 0;
    }

}

?>