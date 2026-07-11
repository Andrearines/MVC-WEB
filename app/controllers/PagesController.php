<?php

namespace controllers;

use app\Core\Router;

class PagesController
{

    public static function indexView(Router $router)
    {
        $router->view('home/index.php', ['inicio' => true, "script" => [], "titulo" => "Home", "title" => "Home"]);
    }

    public static function loginView(Router $router)
    {
        echo "<h1>Auth Login</h1><p>Esta es la página de login (Actualizado en v9.0.0)</p>";
    }
}
