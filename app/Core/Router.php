<?php

namespace app\Core;

class Router
{
    public $rutasGET = [];
    public $rutasPOST = [];
    public $rutasPUT = [];
    public $rutasDELETE = [];
    private $rutasConfig = [];
    
    private $currentPrefix = '';
    private $currentRoles = [];
    private $currentMiddleware = [];
    private $basePath;

    // Track state for fluent chaining
    private $lastRegisteredRoute = '';
    private $currentRouteConfig = [];

    public function __construct($basePath)
    {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    /**
     * Resolves metadata configuration for the matching active route.
     * 
     * @return array
     */
    public function getCurrentRouteConfig(): array
    {
        return $this->currentRouteConfig;
    }

    /**
     * Defines a route group with prefix, roles, and middleware attributes.
     * 
     * @param array $attributes
     * @param callable|string $callback
     * @return void
     */
    public function group(array $attributes, $callback): void
    {
        $previousPrefix = $this->currentPrefix;
        $previousRoles = $this->currentRoles;
        $previousMiddleware = $this->currentMiddleware;

        if (isset($attributes['prefix'])) {
            $this->currentPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }
        if (isset($attributes['roles'])) {
            $this->currentRoles = array_merge($previousRoles, (array)$attributes['roles']);
        }
        if (isset($attributes['middleware'])) {
            $this->currentMiddleware = array_merge($previousMiddleware, (array)$attributes['middleware']);
        }

        if (is_callable($callback)) {
            $callback($this);
        } elseif (is_string($callback) && file_exists($callback)) {
            $router = $this;
            include $callback;
        }

        $this->currentPrefix = $previousPrefix;
        $this->currentRoles = $previousRoles;
        $this->currentMiddleware = $previousMiddleware;
    }

    protected function formatUrl($url)
    {
        $prefix = $this->currentPrefix ? '/' . trim($this->currentPrefix, '/') : '';
        $url = '/' . trim($url, '/');
        
        if ($url === '/') {
            return $prefix ?: '/';
        }
        
        return $prefix . $url;
    }

    private function registerRoute($method, $url, $fn, $config)
    {
        $urlFormatted = $this->formatUrl($url);
        
        // Normalize config parameter (can be role string or array, or a structured config array)
        if (is_string($config)) {
            $config = ['roles' => [$config]];
        } elseif (is_array($config) && !isset($config['roles']) && !isset($config['permissions']) && !isset($config['middleware'])) {
            // Check if it's a simple list of roles, e.g. ['admin']
            $config = ['roles' => $config];
        }

        $roles = array_merge($this->currentRoles, $config['roles'] ?? []);
        $permissions = $config['permissions'] ?? [];
        $middleware = array_merge($this->currentMiddleware, $config['middleware'] ?? []);

        $property = "rutas" . $method;
        $this->{$property}[$urlFormatted] = $fn;
        
        $this->rutasConfig[$urlFormatted] = [
            'roles' => $roles,
            'permissions' => $permissions,
            'middleware' => $middleware
        ];

        $this->lastRegisteredRoute = $urlFormatted;
        return $this;
    }

    public function get($url, $fn, $config = [])
    {
        return $this->registerRoute('GET', $url, $fn, $config);
    }

    public function post($url, $fn, $config = [])
    {
        return $this->registerRoute('POST', $url, $fn, $config);
    }

    public function put($url, $fn, $config = [])
    {
        return $this->registerRoute('PUT', $url, $fn, $config);
    }

    public function delete($url, $fn, $config = [])
    {
        return $this->registerRoute('DELETE', $url, $fn, $config);
    }

    /**
     * Add middleware fluently to the last registered route.
     * 
     * @param string|array $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        if ($this->lastRegisteredRoute) {
            $middlewares = (array)$middleware;
            $this->rutasConfig[$this->lastRegisteredRoute]['middleware'] = array_merge(
                $this->rutasConfig[$this->lastRegisteredRoute]['middleware'] ?? [],
                $middlewares
            );
        }
        return $this;
    }

    public function view($view, $datos = [], $layout = null)
    {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        // Support module views starting with @
        if (str_starts_with($view, '@')) {
            $parts = explode('/', ltrim($view, '@'), 2);
            $moduleName = $parts[0];
            $viewPath = $parts[1] ?? '';
            $viewFile = $this->basePath . "modules/$moduleName/views/$viewPath";
        } else {
            $viewFile = $this->basePath . "app/views/$view";
        }

        ob_start();
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "Vista [{$view}] no encontrada en {$viewFile}";
        }
        $container = ob_get_clean();

        $urlActual = $_SERVER['PATH_INFO'] ?? "/";
        $config = $this->rutasConfig[$urlActual] ?? ['roles' => [], 'permissions' => [], 'middleware' => []];

        $layoutIncluded = false;
        
        if ($layout) {
            if (str_starts_with($layout, '@')) {
                $parts = explode('/', ltrim($layout, '@'), 2);
                $moduleName = $parts[0];
                $layoutPath = $parts[1] ?? '';
                $layoutFile = $this->basePath . "modules/$moduleName/views/layouts/$layoutPath.php";
            } else {
                $layoutFile = $this->basePath . "app/views/layouts/$layout.php";
            }
            if (file_exists($layoutFile)) {
                include $layoutFile;
                $layoutIncluded = true;
            }
        }

        if (!$layoutIncluded && !empty($config['roles'])) {
            foreach ($config['roles'] as $area) {
                if (str_contains($urlActual, $area)) {
                    $layoutFile = $this->basePath . "app/views/layouts/" . $area . ".php";
                    if (file_exists($layoutFile)) {
                        include $layoutFile;
                        $layoutIncluded = true;
                        break;
                    }
                }
            }
        }

        if (!$layoutIncluded) {
            $defaultLayout = $this->basePath . "app/views/layouts/layout.php";
            if (file_exists($defaultLayout)) {
                include $defaultLayout;
            } else {
                echo $container;
            }
        }
    }

    public function Rutas()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $request = new Request();
        $urlActual = $request->getPath();
        $metodo = $request->getMethod();

        $propiedadRutas = "rutas" . $metodo;
        $fn = null;

        if (property_exists($this, $propiedadRutas)) {
            $fn = $this->{$propiedadRutas}[$urlActual] ?? null;
        }

        if (!$fn) {
            http_response_code(404);
            echo "Página no encontrada";
            return;
        }

        $this->currentRouteConfig = $this->rutasConfig[$urlActual] ?? [
            'roles' => [],
            'permissions' => [],
            'middleware' => []
        ];

        // Gather middlewares to run
        $middlewares = $this->currentRouteConfig['middleware'] ?? [];

        // Auto-append Auth & Permission Middlewares if roles or permissions are required
        if (!empty($this->currentRouteConfig['roles']) || !empty($this->currentRouteConfig['permissions'])) {
            if (!in_array(\app\Core\Middleware\AuthMiddleware::class, $middlewares)) {
                array_unshift($middlewares, \app\Core\Middleware\AuthMiddleware::class);
            }
            if (!in_array(\app\Core\Middleware\PermissionMiddleware::class, $middlewares)) {
                $middlewares[] = \app\Core\Middleware\PermissionMiddleware::class;
            }
        }

        // Middleware onion-pattern execution pipeline
        $next = function($request) use ($fn) {
            call_user_func($fn, $this, $request);
        };

        foreach (array_reverse($middlewares) as $middlewareClass) {
            $next = function($request) use ($middlewareClass, $next) {
                if (class_exists($middlewareClass)) {
                    $middleware = new $middlewareClass();
                    return $middleware->handle($request, $this, $next);
                }
                return $next($request);
            };
        }

        // Run the pipeline
        $next($request);
    }
}
