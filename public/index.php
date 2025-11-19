<?php
require_once __DIR__ . '/../config/app.php';

use controllers\PagesController;
use controllers\API\API;
use MVC\Router;

$r = new Router;
$r->get("/", [PagesController::class, 'indexView']);
$r->post("/", [PagesController::class, 'indexView']);
// API Routes
$r->get("/api/servicios", [API::class, 'servicios']);
$r->post("/api/servicios/crear", [API::class, 'crearServicio']);
$r->put("/api/servicios/actualizar", [API::class, 'actualizarServicio']);
$r->delete("/api/servicios/eliminar", [API::class, 'eliminarServicio']);
$r->Rutas();
