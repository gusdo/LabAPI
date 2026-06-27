<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../src/Auth/AuthService.php";
require_once __DIR__ . "/../Modelo/usuario.php";

use Aarom\LabApi\Auth\AuthService;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();



if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        "error" => "Método no permitido. Utilice POST."
    ]);

    exit();
}

$input = json_decode(
    file_get_contents("php://input"),
    true
);

$usuario = trim($input['usuario'] ?? '');
$password = trim($input['password'] ?? '');

if (!$usuario || !$password) {

    http_response_code(400);

    echo json_encode([
        "error" => "Datos incompletos. Se requiere usuario y password."
    ]);

    exit();
}

$usuarioDB = Usuario::buscar($usuario);

if (
    $usuarioDB &&
    password_verify(
        $password,
        $usuarioDB["password"]
    )
) {

    $authService = new AuthService();

    $token = $authService->generarToken(
        $usuarioDB["id"],
        $usuarioDB["usuario"]
    );

    http_response_code(200);

    echo json_encode([
        "mensaje" => "Autenticación exitosa.",
        "token" => $token
    ]);

} else {

    http_response_code(401);

    echo json_encode([
        "error" => "Usuario o contraseña incorrectos."
    ]);
}