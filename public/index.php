<?php
require_once __DIR__ . '/../config/app.php';

use controllers\PagesController;
use MVC\Router;

$r = new Router;
$r->get("/", [PagesController::class, 'indexView']);
$r->post("/", [PagesController::class, 'indexView']);

// Add role-based access
$r->setRol(['admin', 'user']);

// Set area for layout
$r->setArea(['admin']);

$r->get("/admin/index", [PagesController::class, 'indexView']);


$r->Rutas();
