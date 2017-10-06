<?php
use GDO\UI\GDT_Icon;
use GDO\UI\GDT_Tooltip;
$field instanceof GDT_Tooltip;
?>
<div class="gdo-tooltip">
  <?= GDT_Icon::iconS('help'); ?>
  <md-tooltip md-direction="right"><?= $field->tooltip; ?></md-tooltip>
</div>
