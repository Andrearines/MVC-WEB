<?php

namespace app\Core\Auth;

class Authorization
{
    /**
     * Role to permissions mapping.
     * In a robust implementation, this can be retrieved from a database.
     * 
     * @var array
     */
    protected static $rolePermissions = [
        'admin' => ['view-admin', 'edit-settings', 'manage-users'],
        'user' => ['view-profile']

    ];

    /**
     * Checks if the logged-in user possesses a specific role.
     * 
     * @param string $role
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userRole = $_SESSION['rol'] ?? null;
        return $userRole === $role;
    }

    /**
     * Checks if the logged-in user possesses a specific permission.
     * 
     * @param string $permission
     * @return bool
     */
    public static function hasPermission(string $permission): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Also support storing permissions array directly in the session
        if (isset($_SESSION['permissions']) && is_array($_SESSION['permissions'])) {
            if (in_array($permission, $_SESSION['permissions'])) {
                return true;
            }
        }

        $userRole = $_SESSION['rol'] ?? null;
        if (!$userRole) {
            return false;
        }

        $permissions = self::$rolePermissions[$userRole] ?? [];
        return in_array($permission, $permissions);
    }
}
