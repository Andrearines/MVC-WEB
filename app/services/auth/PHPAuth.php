<?php

namespace services\auth;

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
        return $token;
    }

    public function Verify_password($hash, $password)
    {
        return password_verify($password, $hash);
    }

    public function Password_hash($password = null)
    {
        $password = $password ?? $this->password;
        $this->password = password_hash($password, PASSWORD_ARGON2ID);
        return $this->password;
    }
    public function login($data = [])
    {
        session_start();
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
    public function logout()
    {
        session_start();
        $_SESSION = [];
        session_destroy();
    }
    public function is_logged_in()
    {
        session_start();
        return isset($_SESSION['user_id']);
    }
    public function whoami()
    {
        session_start();
        return $_SESSION;
    }
}