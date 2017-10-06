<?php /** @var $field \GDO\DB\GDT_Int **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <input
   type="number"
   name="form[<?= $field->name; ?>]"
   <?= $field->htmlDisabled(); ?>
   <?= $field->htmlRequired(); ?>
   min="<?= $field->min; ?>"
   max="<?= $field->max; ?>"
   step="<?= $field->step; ?>"
   value="<?= $field->getVar(); ?>" />
  <div class="gdo-form-error"><?= $field->error; ?></div>
</div>
