<?php
use GDO\File\GDT_File;
use GDO\Form\GDT_Submit;
use GDO\UI\GDT_IconButton;
$field instanceof GDT_File;
?>
<div
 ng-app="gdo6"
 class="gdo-container<?= $field->classError(); ?>">

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

<div
 ng-controller="GDOUploadCtrl"
 flow-init="{target: '<?= $field->getAction(); ?>', singleFile: <?= $field->multiple?'false':'true'; ?>, fileParameterName: '<?= $field->name; ?>', testChunks: false}"
 flow-file-progress="onFlowProgress($file, $flow, $message);"
 flow-file-success="onFlowSuccess($file, $flow, $message);"
 flow-file-removed="onRemoveFile($file, $flow);"
 flow-file-error="onFlowError($file, $flow, $message);"
 flow-files-submitted="onFlowSubmitted($flow);"
 ng-init='initGDOConfig(<?= $field->displayJSON(); ?>, "#gwffile_<?= $field->name; ?>");'>

  <?= $field->htmlTooltip(); ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <?= $field->htmlIcon(); ?>
  
  <input
   flow-btn
   type="file"
   <?= $field->htmlDisabled(); ?>
   name=""/>

  <div>
    <img ng-repeat="img in $flow.files" flow-img="img" />
  </div>

  <div class="gdo-form-error"><?= $field->error; ?></div>
</div>

</div>
