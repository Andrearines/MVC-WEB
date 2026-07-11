<?php

namespace app\Core\Middleware;

use app\Core\Request;
use app\Core\Router;
use app\Core\Auth\Authorization;

class PermissionMiddleware implements Middleware
{
    /**
     * Handle authorization & permission verification check.
     * 
     * @param Request $request
     * @param Router $router
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, Router $router, callable $next)
    {
        $config = $router->getCurrentRouteConfig();
        $roles = $config['roles'] ?? [];
        $permissions = $config['permissions'] ?? [];

        // Validate Roles if defined
        if (!empty($roles)) {
            $hasRole = false;
            foreach ($roles as $role) {
                if (Authorization::hasRole($role)) {
                    $hasRole = true;
                    break;
                }
            }
            if (!$hasRole) {
                $this->deny($request, "Necesitas el rol: " . implode(' o ', $roles));
            }
        }

        // Validate Permissions if defined
        if (!empty($permissions)) {
            $hasPermission = false;
            foreach ($permissions as $permission) {
                if (Authorization::hasPermission($permission)) {
                    $hasPermission = true;
                    break;
                }
            }
            if (!$hasPermission) {
                $this->deny($request, "Necesitas el permiso: " . implode(' o ', $permissions));
            }
        }

        return $next($request);
    }

    /**
     * Reject access and output 403 Forbidden.
     * 
     * @param Request $request
     * @param string $message
     * @return void
     */
    protected function deny(Request $request, string $message): void
    {
        http_response_code(403);
        
        if (str_starts_with($request->getPath(), '/api')) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Acceso denegado',
                'details' => $message
            ]);
            exit;
        }

        echo "❌ Acceso denegado. {$message}<br>";
        echo "<a href='/'>Volver al inicio</a>";
        exit;
    }
}
