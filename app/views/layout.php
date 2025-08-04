<?php

if(!isset($_SESSION)){
session_start();
}
$auth = $_SESSION['admin'] ?? null;
if(!isset($barra_frotante)){
    $barra_frotante=false;
}
if(!isset($inicio)){

    $inicio=false;

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../build/css/app.css">
    <title></title>
</head>
<body>

    <header class="header 
    <?php echo $inicio ?"inicio":"" ?>
    <?php echo $barra_frotante ?"barra-frotante":"" ?>
    ">
        <div class="contenido-header">

            <div class="barra">
                
           </div> <!--barra-->

           <?php if($inicio){ ?>
           <h1> </h1><!--titulo-->
           <?php }?>
        </div>
    </header>

    <?php echo $contenedor?>
   
    
<footer class="footer">
        <div class="contenedor contenedor-footer">
            <nav class="navegacion">
                
            </nav>
        </div>
        <?php 
        
        $fecha =date("y");

        ?>
        <p class="copyright">todos los derechos resevados <?php echo(date("Y"))?> &copy;</p>
    </footer>
          <?php
    if($script){
        echo "<script src='build/js/{$script}.js'></script>";
    }
    ?>
    <script src="/build/js/modernizr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/build/js/sweetalert-config.js"></script>
    </body>
    </html>


    