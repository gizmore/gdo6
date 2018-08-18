<?php /** @var $field \GDO\File\GDT_File **/ ?>
<div class="gdo-file-controls">
<?php foreach ($field->getInitialFiles() as $file) : $file instanceof \GDO\File\GDO_File; ?>
<?php $deleteButton = sprintf('<input type="submit" name="delete_%s[%s]" value="Remove File" />', $field->name, $file->getID()); #->href($_SERVER['REQUEST_URI']); ?>
<?php if ($field->preview && $file->isImageType()) : ?>
<?php printf('<div class="gdo-file-preview"><img src="%s" />%s (%s)</div>', $field->displayPreviewHref($file), $deleteButton, html($file->getName())); ?>
<?php else : ?>
<?php printf('<div class="gdo-file-preview">%s %s</div>', html($file->getName()), $deleteButton); ?>
<?php endif; ?>
<?php endforeach; ?>
</div>
<div class="gdo-container<?= $field->classError(); ?>">
  <label><?= $field->displayLabel() ?></label>
  <input type="file" name="<?=$field->name?>" />
  <?= $field->htmlError(); ?>
</div>
