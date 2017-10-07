<?php /** @var $field \GDO\UI\GDT_Message **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <?= $field->htmlIcon(); ?>
  <textarea
   name="form[<?= $field->name; ?>]"
   rows="6"
   maxRows="6"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>><?= $field->displayVar(); ?></textarea>
  <div class="gdo-form-error"><?= $field->error; ?></div>
</div>
