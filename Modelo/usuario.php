<?php

require_once "conexion.php";

class Usuario
{
    public static function existe($usuario)
    {
        $db = new DB();
        $cn = $db->conectar();

        $stmt = $cn->prepare(
            "SELECT id FROM usuarios WHERE usuario=?"
        );

        $stmt->execute([$usuario]);

        return $stmt->fetch();
    }

    public static function registrar($usuario, $passwordHash)
    {
        $db = new DB();
        $cn = $db->conectar();

        $stmt = $cn->prepare(
            "INSERT INTO usuarios(usuario,password)
             VALUES(?,?)"
        );

        return $stmt->execute([
            $usuario,
            $passwordHash
        ]);
    }

    public static function buscar($usuario)
    {
        $db = new DB();
        $cn = $db->conectar();

        $stmt = $cn->prepare(
            "SELECT * FROM usuarios
             WHERE usuario=?"
        );

        $stmt->execute([$usuario]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}