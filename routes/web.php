<?php

use app\controllers\PagesController;

$router->get("/", [PagesController::class, 'indexView']);
$router->post("/", [PagesController::class, 'indexView']);
$router->get("/auth/login", [PagesController::class, 'loginView']);
