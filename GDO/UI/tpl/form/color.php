<?php
use GDO\UI\GDT_Color;
$field instanceof GDT_Color;
?>
<md-input-container class="md-block md-float md-icon-left<?= $field->classError(); ?>" flex>
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <?= $field->htmlIcon(); ?>
  <input
   type="color"
   name="form[<?= $field->name; ?>]"
   value="<?= html($field->getVar()); ?>"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>/>
  <div class="gdo-error"><?= $field->error; ?></div>
</md-input-container>
