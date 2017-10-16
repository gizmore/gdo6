<?php /** @var $gdo \GDO\File\GDO_File **/
use GDO\UI\GDT_Icon; ?>
<div class="gdo-file">
<?php if ($gdo) : ?>
  <?= GDT_Icon::iconS('file'); ?>
  <span class="gdo-file-size"><?= $gdo->displaySize(); ?></span>
  <span class="gdo-file-type"><?= $gdo->getType(); ?></span>
  <div class="cf"></div>
<?php endif; ?>
</div>
