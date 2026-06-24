<?php

require_once __DIR__ . '/../Modelo/usuario.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Método no permitido."
    ]);

    exit();
}

$input = json_decode(
    file_get_contents("php://input"),
    true
);

$usuario = trim($input["usuario"] ?? "");
$password = trim($input["password"] ?? "");

if (!$usuario || !$password) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Debe completar todos los campos."
    ]);

    exit();
}

if (Usuario::existe($usuario)) {

    http_response_code(409);

    echo json_encode([
        "success" => false,
        "message" => "El usuario ya existe."
    ]);

    exit();
}

$passwordHash = password_hash(
    $password,
    PASSWORD_BCRYPT
);

if (
    Usuario::registrar(
        $usuario,
        $passwordHash
    )
) {

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Usuario registrado correctamente."
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Error al registrar el usuario."
    ]);
}
?>