<?php /** @var $field \GDO\File\GDT_ImageFile **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label><?= $field->displayLabel() ?></label>
  <?=$field->htmlIcon()?>
  <?=$field->htmlTooltip()?>
<?php if ($field->getInitialFiles()) : ?>
<div class="gdo-imagefile-preview">
<?php foreach ($field->getInitialFiles() as $file) : ?>
<img src="<?= $field->displayPreviewHref($file); ?>" />
<?php endforeach; ?>
</div>
<?php endif; ?>
  <input type="file" name="<?=$field->name?>" />
  <?= $field->htmlError(); ?>
</div>
