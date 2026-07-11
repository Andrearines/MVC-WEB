<?php

namespace modules\Blog\controllers;

use app\Core\Router;

class BlogController
{
    public static function index(Router $router)
    {
        // Render custom module view
        $router->view('@Blog/index.php', [
            'titulo' => 'Blog Module',
            'script' => [],
            'title' => 'Blog Module',
            'posts' => [
                ['id' => 1, 'title' => 'Novedades de la v9.0.0', 'content' => '¡Vite, Módulos y Plugins!'],
                ['id' => 2, 'title' => 'Estructura Modular', 'content' => 'Las vistas, controladores y rutas agrupadas en un mismo lugar.']
            ]
        ]);
    }
}
