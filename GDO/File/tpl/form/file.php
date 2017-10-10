<?php /** @var $field \GDO\File\GDT_File **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label><?= $field->displayLabel() ?></label>
  <div>Deine Mudda</div>
  <input type="file" name="<?=$field->name?>" />
  <div class="gdo-form-error"><?= $field->error; ?></div>
</div>
