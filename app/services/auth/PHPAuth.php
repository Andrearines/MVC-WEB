<?php

namespace services\auth;

use services\Logger;

class PHPAuth
{
    private $token;
    private $password;

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public function create_token()
    {
        $token = bin2hex(random_bytes(5));
        $this->token = $token;
        Logger::info("Token creado exitosamente: " . $token);
        return $token;
    }

    public function Verify_password($hash, $password)
    {
        Logger::info("Contraseña verificada exitosamente: " . $password);
        Logger::info("Hash de la contraseña: " . $hash);
        return password_verify($password, $hash);
    }

    public function Password_hash($password = null)
    {
        $password = $password ?? $this->password;
        $this->password = password_hash($password, PASSWORD_ARGON2ID);
        Logger::info("Hash de la contraseña creado exitosamente: " . $this->password);
        return $this->password;
    }
    public function login($data = [])
    {
        session_start();
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
        Logger::info("Login exitoso: " . $data['user_id']);
    }
    public function logout()
    {
        session_start();
        $_SESSION = [];
        session_destroy();
        Logger::info("Logout exitoso");
    }
    public function is_logged_in()
    {
        session_start();
        Logger::info("Usuario logueado exitosamente: " . $_SESSION['user_id']);
        return isset($_SESSION['user_id']);
    }
    public function whoami()
    {
        session_start();
        Logger::info("Usuario actual: " . $_SESSION['user_id']);
        return $_SESSION;
    }
}