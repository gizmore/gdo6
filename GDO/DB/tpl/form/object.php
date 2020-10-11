<?php /** @var $field \GDO\DB\GDT_Object **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?=$field->htmlIcon()?>
  <label for="form[<?= $field->name; ?>]"><?= $field->displayLabel(); ?></label>
  <input
   type="number"
   step="1"
   name="form[<?= $field->name; ?>]"
   value="<?= $field->displayVar(); ?>"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?> />
  <?= $field->htmlError(); ?>
</div>
