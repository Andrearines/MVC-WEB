<?php
require "utilis.php";
require "Environment.php";
require __DIR__ . "/../db/database.php";
require __DIR__ . "/../vendor/autoload.php";

// Cargar variables de entorno al inicio
Environment::load();

use app\models\Main;

Main::setDb(conectaDB());
