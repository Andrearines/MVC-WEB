<?php

namespace services\auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/../../config/Environment.php";
\Environment::load();


class JWTAuth
{
    private static $key = null;

    public function __construct()
    {
        $this->key = self::getKey();
    }

    private static function getKey()
    {
        if (self::$key === null) {
            self::$key = \Environment::get('JWT_KEY', '');
        }
        return self::$key;
    }

    public function desifrartoken()
    {
        $key = $this->key;
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

        $key = $this->key;
        $token = JWT::encode($payload, $key, 'HS256');

        setcookie("access_token", $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}