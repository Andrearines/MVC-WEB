<?php

use controllers\API\API;

// API Version 1 routes (automatically prefixed with /api/v1)
$router->group(['prefix' => 'v1'], function ($router) {
    $router->get("/servicios", [API::class, 'servicios']);
    $router->post("/servicios", [API::class, 'crearServicio']);
    $router->put("/servicios", [API::class, 'actualizarServicio']);
    $router->delete("/servicios", [API::class, 'eliminarServicio']);
});
