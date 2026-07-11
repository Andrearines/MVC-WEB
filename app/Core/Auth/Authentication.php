<?php

namespace app\Core\Auth;

class Authentication
{
    /**
     * Verifies if the user is authenticated in the active session.
     * 
     * @return bool
     */
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) || (isset($_SESSION['login']) && $_SESSION['login'] === true);
    }

    /**
     * Resolves the authenticated user identifier.
     * 
     * @return mixed|null
     */
    public static function user()
    {
        if (!self::check()) {
            return null;
        }
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Registers a user login session.
     * 
     * @param array $data
     * @return void
     */
    public static function login(array $data): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['login'] = true;
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Destroys the active user authentication session.
     * 
     * @return void
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
    }
}
