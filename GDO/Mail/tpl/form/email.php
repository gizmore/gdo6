<?php
use GDO\Mail\GDT_Email;
use GDO\UI\GDT_Icon;
$field instanceof GDT_Email;
?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?= GDT_Icon::iconS('email'); ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <input
   type="email"
   name="form[<?= $field->name; ?>]"
   value="<?= $field->displayVar(); ?>"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?> />
  <?= $field->htmlError(); ?>
</div>
