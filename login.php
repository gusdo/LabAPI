<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Auth/AuthService.php';

use Aarom\LabApi\Auth\AuthService;
// Encabezados globales CORS y de contenido
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Soporte para peticiones preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Validar que sea estrictamente una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Utilice POST."]);
    exit();
}

// Leer y decodificar el JSON de la petición
$input = json_decode(file_get_contents("php://input"), true);
$usuario = isset($input['usuario']) ? trim($input['usuario']) : null;
$password = isset($input['password']) ? trim($input['password']) : null;

// Validar campos obligatorios
if (!$usuario || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos. Se requiere usuario y password."]);
    exit();
}

// Validación de credenciales estáticas para el laboratorio
if ($usuario === "admin" && $password === "admin123") {
    $authService = new AuthService();
    $token = $authService->generarToken(1, $usuario);

    http_response_code(200);
    echo json_encode([
        "mensaje" => "Autenticación exitosa.",
        "token" => $token
    ]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Usuario o contraseña incorrectos."]);
}