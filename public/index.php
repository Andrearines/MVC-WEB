<?php
require_once __DIR__ . '/../config/app.php';

use controllers\PagesController;
use MVC\Router;

$r = new Router;
$r->get("/", [PagesController::class, 'indexView']);
$r->post("/", [PagesController::class, 'indexView']);

$r->get("/admin/index", [PagesController::class, 'indexView'], ['admin']);

//es la ruta predeterminada para el login y cuando no esta autorizado a entrar a una ruta
$r->get("/auth/login", [PagesController::class, 'loginView']);


$r->Rutas();
