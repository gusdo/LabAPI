<?php

header("Content-Type: application/json");

require_once "../Modelo/Producto.php";

$accion = $_REQUEST["accion"] ?? "";

$respuesta = [
    "success" => false,
    "message" => "",
    "data" => null,
    "errors" => []
];

switch($accion){

    case "Guardar":

        $codigo = trim($_POST["codigo"] ?? "");
        $producto = trim($_POST["producto"] ?? "");
        $precio = $_POST["precio"] ?? "";
        $cantidad = $_POST["cantidad"] ?? "";

        $errores = [];

        if($codigo == ""){
            $errores[] = "Debe ingresar un código.";
        }

        if($producto == ""){
            $errores[] = "Debe ingresar un producto.";
        }

        if(!is_numeric($precio) || $precio <= 0){
            $errores[] = "El precio debe ser mayor que 0.";
        }

        if(!is_numeric($cantidad) || $cantidad < 0){
            $errores[] = "La cantidad no puede ser negativa.";
        }

        if(count($errores) > 0){

            $respuesta["message"] = "Errores de validación";
            $respuesta["errors"] = $errores;

            echo json_encode($respuesta);
            exit;
        }

        if(Producto::existeCodigo($codigo)){

            $respuesta["message"] = "Código duplicado";

            $respuesta["errors"][] =
                "Ya existe un producto con ese código.";

            echo json_encode($respuesta);
            exit;
        }

        $p = new Producto(
            $codigo,
            $producto,
            $precio,
            $cantidad
        );

        $respuesta["success"] = $p->guardar();

        $respuesta["message"] =
            "Producto guardado correctamente.";

    break;

    case "Modificar":

        $p = new Producto(
            $_POST["codigo"],
            $_POST["producto"],
            $_POST["precio"],
            $_POST["cantidad"]
        );

        $respuesta["success"] =
            $p->editar($_POST["id"]);

        $respuesta["message"] =
            "Producto actualizado correctamente.";

    break;

    case "Buscar":

        $respuesta["success"] = true;

        $respuesta["data"] =
            Producto::buscar(
                $_GET["codigo"]
            );

    break;

    case "Listar":

        $respuesta["success"] = true;

        $respuesta["data"] =
            Producto::listar();

    break;

    case "Eliminar":

        $respuesta["success"] =
            Producto::eliminar(
                $_POST["id"]
            );

        $respuesta["message"] =
            "Producto eliminado correctamente.";

    break;

    default:

        $respuesta["message"] =
            "Acción no válida.";

    break;
}

echo json_encode($respuesta);

?>