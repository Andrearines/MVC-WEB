<?php

use app\controllers\PagesController;

$router->get("/index", [PagesController::class, 'indexView']);
