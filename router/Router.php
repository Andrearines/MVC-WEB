<?php

namespace MVC;

class Router
{
    public $rutasGET = [];
    public $rutasPOST = [];

    private $areas = [];
    private $rol = [];
    private $rutasConfig = [];

    public function setArea($area)
    {
        if (is_array($area)) {
            $this->areas = array_merge($this->areas, $area);
        } else {
            $this->areas[] = $area;
        }
    }

    public function setRol($rol)
    {
        if (is_array($rol)) {
            $this->rol = array_merge($this->rol, $rol);
        } else {
            $this->rol[] = $rol;
        }
    }

    public function get($url, $fn)
    {
        $this->rutasGET[$url] = $fn;
        $this->rutasConfig[$url] = [
            'areas' => $this->areas,
            'roles' => $this->rol
        ];
        $this->areas = [];
        $this->rol = [];
    }

    public function post($url, $fn)
    {
        $this->rutasPOST[$url] = $fn;
        $this->rutasConfig[$url] = [
            'areas' => $this->areas,
            'roles' => $this->rol
        ];
        $this->areas = [];
        $this->rol = [];
    }

    public function view($view, $datos = [])
    {
        // Extraer datos para la vista
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        // Capturar el contenido de la vista
        ob_start();
        include __DIR__ . "/../app/views/$view";
        $contenedor = ob_get_clean();

        // Obtener URL actual
        $urlActual = $_SERVER['PATH_INFO'] ?? "/";

        // Obtener configuración de esta ruta
        $config = $this->rutasConfig[$urlActual] ?? ['areas' => [], 'roles' => []];

        // Buscar layout específico
        $layoutIncluded = false;

        if (!empty($config['areas'])) {
            foreach ($config['areas'] as $area) {
                if (str_contains($urlActual, $area)) {
                    $layoutFile = __DIR__ . "/../app/views/layouts/" . $area . ".php";

                    if (file_exists($layoutFile)) {
                        include $layoutFile;
                    } else {
                        include __DIR__ . "/../app/views/layouts/layout.php";
                    }

                    $layoutIncluded = true;
                    break;
                }
            }
        }

        // Layout por defecto
        if (!$layoutIncluded) {
            include __DIR__ . "/../app/views/layouts/layout.php";
        }
    }

    public function Rutas()
    {
        // ✅ Iniciar sesión ANTES de verificar roles
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $urlActual = $_SERVER['PATH_INFO'] ?? "/";
        $metodo = $_SERVER['REQUEST_METHOD'];

        // Obtener la función de la ruta
        if ($metodo === 'GET') {
            $fn = $this->rutasGET[$urlActual] ?? null;
        } else {
            $fn = $this->rutasPOST[$urlActual] ?? null;
        }

        if (!$fn) {
            http_response_code(404);
            echo "Página no encontrada";
            return;
        }

        // ✅ VERIFICAR ROLES ANTES de ejecutar el controlador
        $config = $this->rutasConfig[$urlActual] ?? ['areas' => [], 'roles' => []];

        if (!empty($config['roles'])) {
            $userRole = $_SESSION['rol'] ?? null;

            // Si no tiene rol o no tiene el rol correcto
            if (!$userRole || !in_array($userRole, $config['roles'])) {
                http_response_code(403);

                // Si no está logueado, redirigir a login
                if (!$userRole) {
                    header('Location: /auth/login');
                    exit;
                }

                // Si está logueado pero no tiene el rol correcto
                echo "❌ Acceso denegado. Necesitas rol: " . implode(' o ', $config['roles']);
                echo "<br><a href='/'>Volver al inicio</a>";
                exit;
            }
        }

        // ✅ Si pasó la verificación, ejecutar el controlador
        call_user_func($fn, $this);
    }
}
