<div>
  <?php
  if ($alertas && is_array($alertas) && count($alertas) > 0 || $alertas && is_object($alertas) && !empty($alertas)) : ?>
    <input type="hidden" id="alertas" value="<?= json_encode($alertas) ?>">
  <?php
  endif; ?>
</div>
