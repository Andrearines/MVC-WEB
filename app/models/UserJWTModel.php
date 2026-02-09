<?php

namespace models;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/../../config/Environment.php";
\Environment::load();
class UserJWTModel extends UserPHP
{

    public static $table = "users";
    static $columnDB = [];
    static private $key = null;

    private static function getKey()
    {
        if (self::$key === null) {
            self::$key = \Environment::get('JWT_KEY', '');
        }
        return self::$key;
    }


    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function create_token()
    {
        $token = bin2hex(random_bytes(8));
        $this->token = $token;
        return $token;
    }

    public static function desifrartoken()
    {
        $key = self::getKey();
        $token = $_COOKIE['access_token'] ?? null;
        if ($token) {
            try {
                $payload = JWT::decode($token, new Key($key, 'HS256'));
            } catch (\Throwable $e) {
                return false;
            }
            $uid = isset($payload->id) ? (int) $payload->id : 0;
            if ($uid <= 0) {
                return false;
            }
            $user = UserPHP::find($uid);
            if (!$user) {
                return false;
            } else {
                return $user;
            }
        } else {
            return false;
        }
    }


    public function login($payload)
    {

        $key = self::getKey();
        $token = JWT::encode($payload, $key, 'HS256');

        setcookie("access_token", $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    public function verify_password($hash, $password)
    {
        return password_verify($password, $hash);
    }

}
