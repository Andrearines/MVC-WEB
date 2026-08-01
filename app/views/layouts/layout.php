<?php

require_once __DIR__ . '../../../../config/Environment.php';
\Environment::load();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../build/css/app.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo asset_vite('src/main.js'); ?>
    <title> <?php echo Environment::get('APP_NAME') ?> | <?php echo $title ?? "" ?></title>
</head>

<body>

    <?php echo $container ?? "" ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="build/js/app.js"></script>

    <?php
    if (isset($script) && is_array($script)) {
        foreach ($script as $s) {
            echo "<script src='build/js/{$s}.js'></script>";
        }
    }
    ?>
</body>

</html>