<?php

namespace models;

class UserPHP extends Main
{
    public static $table = "users";

    public static $columnDB = ["id", "nombre", "apellido", "email", "password", "confirmado", "token", "admin"];

    public $id;
    public $admin;
    public $nombre;
    public $apellido;
    public $token;
    public $email;
    public $confirmado;
    public $password_c;
    public $password;


    public function __construct($data = [])
    {
        parent::__construct($data);
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
    }

    public function ExisteUser()
    {
        $r = self::findBy("email", $this->email);
        if ($r) {
            return true;
        } else {
            return false;
        }
    }

}
