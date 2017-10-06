<?php
use GDO\UI\GDT_Slider;
$field instanceof GDT_Slider;
?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?= $field->icon; ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <input
   type="range"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>
   min="<?= $field->min; ?>"
   max="<?= $field->max; ?>"
   step="<?= $field->step; ?>"
   name="form[<?= $field->name; ?>]"
   value="<?= $field->getVar(); ?>" />
  <div class="gdo-form-error"><?= $field->error; ?></div>
</div>
 