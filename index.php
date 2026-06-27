<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/src/Auth/AuthService.php";

use Aarom\LabApi\Auth\AuthService;

// Soporte para peticiones preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Validación perimetral del Token JWT
$authService = new AuthService();
$usuarioValidado = $authService->validarTokenDesdeCabecera();

// Carga del controlador si el token es válido
require_once __DIR__ . '/Controlador/productosController.php';