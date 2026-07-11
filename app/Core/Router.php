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
    private $basePath;

    public function __construct($basePath)
    {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    public function group($attributes, $callback)
    {
        $previousPrefix = $this->currentPrefix;
        $previousRoles = $this->currentRoles;

        if (isset($attributes['prefix'])) {
            $this->currentPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }
        if (isset($attributes['roles'])) {
            $this->currentRoles = array_merge($previousRoles, (array)$attributes['roles']);
        }

        if (is_callable($callback)) {
            $callback($this);
        } elseif (is_string($callback) && file_exists($callback)) {
            $router = $this;
            include $callback;
        }

        $this->currentPrefix = $previousPrefix;
        $this->currentRoles = $previousRoles;
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

    public function get($url, $fn, $rol = [])
    {
        $urlFormatted = $this->formatUrl($url);
        $roles = array_merge($this->currentRoles, (array)$rol);

        $this->rutasGET[$urlFormatted] = $fn;
        $this->rutasConfig[$urlFormatted] = [
            'areas' => $roles,
            'roles' => $roles
        ];
    }

    public function post($url, $fn, $rol = [])
    {
        $urlFormatted = $this->formatUrl($url);
        $roles = array_merge($this->currentRoles, (array)$rol);

        $this->rutasPOST[$urlFormatted] = $fn;
        $this->rutasConfig[$urlFormatted] = [
            'areas' => $roles,
            'roles' => $roles
        ];
    }

    public function put($url, $fn, $rol = [])
    {
        $urlFormatted = $this->formatUrl($url);
        $roles = array_merge($this->currentRoles, (array)$rol);

        $this->rutasPUT[$urlFormatted] = $fn;
        $this->rutasConfig[$urlFormatted] = [
            'areas' => $roles,
            'roles' => $roles
        ];
    }

    public function delete($url, $fn, $rol = [])
    {
        $urlFormatted = $this->formatUrl($url);
        $roles = array_merge($this->currentRoles, (array)$rol);

        $this->rutasDELETE[$urlFormatted] = $fn;
        $this->rutasConfig[$urlFormatted] = [
            'areas' => $roles,
            'roles' => $roles
        ];
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
        $config = $this->rutasConfig[$urlActual] ?? ['areas' => [], 'roles' => []];

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

        if (!$layoutIncluded && !empty($config['areas'])) {
            foreach ($config['areas'] as $area) {
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

        $config = $this->rutasConfig[$urlActual] ?? ['areas' => [], 'roles' => []];

        if (!empty($config['roles'])) {
            $userRole = $_SESSION['rol'] ?? null;

            if (!$userRole || !in_array($userRole, $config['roles'])) {
                http_response_code(403);

                if (!$userRole) {
                    header('Location: /auth/login');
                    exit;
                }

                echo "❌ Acceso denegado. Necesitas rol: " . implode(' o ', $config['roles']);
                echo "<br><a href='/'>Volver al inicio</a>";
                exit;
            }
        }

        call_user_func($fn, $this, $request);
    }
}
