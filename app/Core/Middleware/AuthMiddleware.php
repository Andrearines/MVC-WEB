<?php

namespace app\Core\Middleware;

use app\Core\Request;
use app\Core\Router;
use app\Core\Auth\Authentication;

class AuthMiddleware implements Middleware
{
    /**
     * Handle authentication check.
     * 
     * @param Request $request
     * @param Router $router
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, Router $router, callable $next)
    {
        if (!Authentication::check()) {
            // Check if it's an API route and return JSON
            if (str_starts_with($request->getPath(), '/api')) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'No autorizado']);
                exit;
            }
            
            // Redirect web client to login
            header('Location: /auth/login');
            exit;
        }

        return $next($request);
    }
}
