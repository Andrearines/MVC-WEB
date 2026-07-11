<?php

namespace app\Core\Middleware;

use app\Core\Request;
use app\Core\Router;

interface Middleware
{
    /**
     * Handle an incoming request.
     * 
     * @param Request $request
     * @param Router $router
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, Router $router, callable $next);
}
