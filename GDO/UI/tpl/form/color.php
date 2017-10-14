<?php /** @var $field \GDO\UI\GDT_Color **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <input
   type="color"
   name="form[<?= $field->name; ?>]"
   value="<?= html($field->getVar()); ?>"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>/>
  <?= $field->htmlError(); ?>
</div>
