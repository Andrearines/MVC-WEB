<?php

foreach ($alertas as $alerta) {
    foreach ($alerta as $mensaje) {?>
      <p class="alerta <?= $alerta ?>"><?= $mensaje ?></p>
    <?php 
    }
}