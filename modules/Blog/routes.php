<?php

use modules\Blog\controllers\BlogController;

$router->group(['prefix' => 'blog'], function($router) {
    $router->get('/index', [BlogController::class, 'index']);
});
