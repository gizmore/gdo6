<?php
use GDO\File\GDT_File;
$gdo instanceof GDT_File;
?>
<div class="gdo-file">
  <span class="gdo-file-name"><?= html($gdo->getName()); ?></span>
  <span class="gdo-file-size"><?= $gdo->displaySize(); ?></span>
  <span class="gdo-file-type"><?= $gdo->getType(); ?></span>
</div>
