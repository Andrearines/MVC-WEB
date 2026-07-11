<?php

use controllers\PagesController;

$router->get("/index", [PagesController::class, 'indexView']);
