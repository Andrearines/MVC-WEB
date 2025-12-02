<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($barra_frotante)) {
    $barra_frotante = false;
}
if (!isset($inicio)) {

    $inicio = false;
}
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

    <header class="header
    <?php echo $inicio ? "inicio" : "" ?>">

        <div class="barra  <?php echo $barra_frotante ? "barra-frotante" : "" ?>">

        </div> <!--barra-->

        <?php if ($inicio) { ?>
            <h1> </h1><!--titulo-->
        <?php } ?>
    </header>

    <?php echo $contenedor ?>


    <footer class="footer">
        <div class="logo-contenedor">
            <a href=""></a>
        </div>
        <div class="contenedor contenedor-footer">
            <nav class="navegacion">

            </nav>
        </div>

        <!-- <p class="copyright">todos los derechos resevados <?php echo (date("Y")) ?> &copy;</p> -->
    </footer>
    <?php
    if ($script) {
        foreach ($script as $script) {
            echo "<script src='build/js/{$script}.js'></script>";
        }
    }
    ?>
    <script src="/build/js/modernizr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/build/js/sweetalert-config.js"></script>
</body>

</html>
