<?php

header("Content-Type: application/json; charset=UTF-8");
require_once "./Modelo/Producto.php";

$metodo = $_SERVER['REQUEST_METHOD'];

function responder(int $codigo, bool $success, string $message = "", $data = null, array $errors = []): void
{
    http_response_code($codigo);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data,
        "errors" => $errors
    ]);
    exit;
}

function obtenerJson(): array
{
    $datos = json_decode(file_get_contents("php://input"), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        responder(400, false, "JSON inválido.");
    }
    return $datos ?? [];
}

function validarProducto(string $codigo, string $producto, $precio, $cantidad): array
{
    $errores = [];
    if (!trim($codigo))
        $errores[] = "Debe ingresar un código.";
    if (!trim($producto))
        $errores[] = "Debe ingresar un producto.";
    if (!is_numeric($precio) || $precio <= 0)
        $errores[] = "El precio debe ser mayor que 0.";
    if (!is_numeric($cantidad) || $cantidad < 0)
        $errores[] = "La cantidad no puede ser negativa.";
    return $errores;
}

switch ($metodo) {
    case "POST":
        $input = obtenerJson();
        $codigo = trim($input["codigo"] ?? "");
        $producto = trim($input["producto"] ?? "");
        $precio = $input["precio"] ?? null;
        $cantidad = $input["cantidad"] ?? null;

        if ($errores = validarProducto($codigo, $producto, $precio, $cantidad)) {
            responder(400, false, "Errores de validación", null, $errores);
        }

        if (Producto::existeCodigo($codigo)) {
            responder(409, false, "Código duplicado", null, ["Ya existe un producto con ese código."]);
        }

        $nuevo = new Producto($codigo, $producto, $precio, $cantidad);
        if ($nuevo->guardar()) {
            responder(201, true, "Producto guardado correctamente.");
        }
        responder(500, false, "Error al guardar en la base de datos.");
        break;

    case "PUT":
        $input = obtenerJson();
        $id = $input["id"] ?? null;
        if (!$id)
            responder(400, false, "Debe indicar el ID del producto.");

        $codigo = trim($input["codigo"] ?? "");
        $producto = trim($input["producto"] ?? "");
        $precio = $input["precio"] ?? null;
        $cantidad = $input["cantidad"] ?? null;

        if ($errores = validarProducto($codigo, $producto, $precio, $cantidad)) {
            responder(400, false, "Errores de validación", null, $errores);
        }

        $actualizado = new Producto($codigo, $producto, $precio, $cantidad);
        if ($actualizado->editar($id)) {
            responder(200, true, "Producto actualizado correctamente.");
        }
        responder(500, false, "No se pudo actualizar el producto.");
        break;

    case "GET":
        $codigo = $_GET["codigo"] ?? null;
        if ($codigo) {
            $producto = Producto::buscar($codigo);
            if (!$producto)
                responder(404, false, "Producto no encontrado.");
            responder(200, true, "", $producto);
        }
        responder(200, true, "", Producto::listar());
        break;

    case "DELETE":
        $input = json_decode(file_get_contents("php://input"), true) ?? [];
        $id = $_GET["id"] ?? ($input["id"] ?? null);

        if (!$id)
            responder(400, false, "Debe indicar el ID del producto.");

        if (Producto::eliminar($id)) {
            responder(200, true, "Producto eliminado correctamente.");
        }
        responder(500, false, "No se pudo eliminar el producto.");
        break;

    default:
        responder(405, false, "Método HTTP no soportado.");
}