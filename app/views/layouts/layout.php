<?php

require_once __DIR__ . '../../../../config/Environment.php';
\Environment::load();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../build/css/app.css">
    <title> <?php echo Environment::get('APP_NAME') ?> | <?php echo $titulo ?></title>
</head>

<body>

    <?php echo $contenedor ?>

    <?php
    if ($script) {
        foreach ($script as $script) {
            echo "<script src='build/js/{$script}.js'></script>";
        }
    }
    ?>
    <script src="/build/js/base/js/modernizr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/build/js/base/js/sweetalert-config.js"></script>
</body>

</html>
