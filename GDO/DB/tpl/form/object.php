<?php /** @var $field \GDO\DB\GDT_Object **/ ?>
<div class="gdt-container<?= $field->classError(); ?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?= $field->displayLabel(); ?></label>
  <input
   <?=$field->htmlID()?>
   type="number"
   step="1"
   <?=$field->htmlFormName()?>
   value="<?= $field->display(); ?>"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?> />
  <?= $field->htmlError(); ?>
</div>
