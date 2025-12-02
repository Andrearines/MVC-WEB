<?php

namespace MVC;

class Router
{

    public $rutasGET = [];
    public $rutasPOST = [];
    public $areas = [];
    public $rol = [];

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
        $this->rol[] = $rol;
    }

    public function get($url, $fn)
    {
        $this->rutasGET[$url] = $fn;
    }
    public function post($url, $fn)
    {
        $this->rutasPOST[$url] = $fn;
    }

    public function view($view, $datos = [])
    {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include __DIR__ . "/../app/views/$view";

        $contenedor = ob_get_clean();

        $urlActual = $_SERVER['PATH_INFO'] ?? "/";
        for ($i = 0; $i < count($this->areas); $i++) {
            if (str_contains($urlActual, $this->areas[$i])) {

                $layoutFile = __DIR__ . "/../app/views/layouts/" . $this->areas[$i] . ".php";
                if (file_exists($layoutFile)) {

                    // Check if role-based access is required
                    if (!empty($this->rol) && !in_array($_SESSION['rol'] ?? '', $this->rol)) {
                        // Role not allowed, show error or redirect
                        http_response_code(403);
                        echo "Acceso denegado";
                        exit;
                    }
                    include $layoutFile;
                } else {
                    // Fallback to default layout if specific layout doesn't exist
                    if (!empty($this->rol) && !in_array($_SESSION['rol'] ?? '', $this->rol)) {
                        // Role not allowed, show error or redirect
                        http_response_code(403);
                        echo "Acceso denegado";
                        exit;
                    }
                    include __DIR__ . "/../app/views/layouts/layout.php";
                }
            } else {
                // For non-admin routes, use default layout
                include __DIR__ . "/../app/views/layouts/layout.php";
            }
        }
    }

    public function Rutas()
    {
        $urlA = $_SERVER['PATH_INFO'] ?? "/";
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($metodo === 'GET') {
            $fn = $this->rutasGET[$urlA] ?? null;
        } else {
            $fn = $this->rutasPOST[$urlA] ?? null;
        }

        if ($fn) {
            call_user_func($fn, $this);
        } else {
            echo "pagina no existe";
        }
    }
}
