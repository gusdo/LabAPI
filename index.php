<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Auth/AuthService.php';

use Aarom\LabApi\Auth\AuthService;

// Cabeceras HTTP globales para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Soporte para peticiones preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Validación perimetral del Token JWT
$authService = new AuthService();
$usuarioValidado = $authService->validarTokenDesdeCabecera();

// Carga del controlador si el token es válido
require_once __DIR__ . '/Controlador/productos.php';