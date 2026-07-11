<?php

// Require Composer Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use app\Core\Application;

// Initialize Application
$app = new Application(__DIR__ . '/../');

// Run application routing & plugins lifecycle
$app->run();
