<?php
namespace Aarom\LabApi\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthService
{
    private $secret_key;
    private $encrypt_algorithm;

    public function __construct()
    {
        $this->secret_key = $_ENV['JWT_SECRET_KEY'];
        $this->encrypt_algorithm = 'HS256';
    }

    public function generarToken($usuarioId, $usuarioNombre)
    {
        $tiempo_actual = time();
        $payload = [
            'iss' => 'http://localhost',
            'aud' => 'http://localhost',
            'iat' => $tiempo_actual,
            'exp' => $tiempo_actual + 3600, // 1 hora de duración
            'data' => [
                'id' => $usuarioId,
                'username' => $usuarioNombre
            ]
        ];

        return JWT::encode($payload, $this->secret_key, $this->encrypt_algorithm);
    }

    public function validarTokenDesdeCabecera()
    {
        // Pasamos todas las llaves de las cabeceras a minúsculas para buscarlas directamente
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        $authHeader = $headers['authorization'] ?? null;

        if (is_array($authHeader)) {
            $authHeader = $authHeader[0];
        }

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(["error" => "Acceso denegado. Falta el token de seguridad."]);
            exit();
        }

        // Extraer el token usando desestructuración rápida o validando el prefijo Bearer
        $partes = explode(" ", $authHeader);
        $token = (isset($partes[1])) ? trim($partes[1]) : null;

        if (!$token) {
            http_response_code(401);
            echo json_encode(["error" => "Formato de token inválido. Use Bearer [Token]"]);
            exit();
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, $this->encrypt_algorithm));
            return $decoded->data;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => "Token inválido o expirado.", "detalles" => $e->getMessage()]);
            exit();
        }
    }
}