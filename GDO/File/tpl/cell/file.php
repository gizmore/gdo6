<?php /** @var $gdo \GDO\File\GDO_File **/
use GDO\UI\GDT_Icon; ?>
<?php if (!$gdo) return; ?>
<div class="gdo-file">
<?php if ($gdo->isImageType()) : ?>
<img
 style="display: block;"
 src="<?= href('File', 'GetFile', '&file='.$gdo->getID()); ?>" />
<?php else : ?>
  <?= GDT_Icon::iconS('file'); ?>
<?php endif; ?>
  <span class="gdo-file-title"><?= $gdo->displayName(); ?></span>
  <span class="gdo-file-size"><?= $gdo->displaySize(); ?></span>
  <span class="gdo-file-type"><?= $gdo->getType(); ?></span>
  <div class="cf"></div>
</div>
