<?php
/** @var $gdo \GDO\File\GDO_File **/
/** @var $field \GDO\File\GDT_File **/
use GDO\UI\GDT_Icon; ?>
<?php if (!$gdo) return; ?>
<div class="gdo-file">
<?php if ($gdo->isImageType()) : ?>
<img
 style="display: block; max-width: 100%; <?php #$field->styleSize()?>"
 src="<?=$field->displayPreviewHref($gdo)?>" />
 
<?php else : ?>
  <?= GDT_Icon::iconS('file'); ?>
<?php endif; ?>
<?php if ($field->withFileInfo) : ?>
  <span class="gdo-file-title"><?= $gdo->displayName(); ?></span>
  <span class="gdo-file-size"><?= $gdo->displaySize(); ?></span>
  <span class="gdo-file-type"><?= $gdo->getType(); ?></span>
  <div class="cf"></div>
<?php endif; ?>
</div>
