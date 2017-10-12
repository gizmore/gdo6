<?php /** @var $gdo \GDO\File\GDO_File **/ ?>
<div class="gdo-file">
<?php if ($gdo) : ?>
  <span class="gdo-file-name"><?= html($gdo->getName()); ?></span>
  <span class="gdo-file-size"><?= $gdo->displaySize(); ?></span>
  <span class="gdo-file-type"><?= $gdo->getType(); ?></span>
<?php endif; ?>
</div>
