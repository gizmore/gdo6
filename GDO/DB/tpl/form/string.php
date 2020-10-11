<?php /** @var $field \GDO\DB\GDT_String **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->displayLabel(); ?></label>
  <input
   type="text"
   <?=$field->htmlID()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlPattern()?>
   <?=$field->htmlDisabled()?>
   min="<?=$field->min?>"
   max="<?=$field->max?>"
   size="<?=min($field->max, 32) ?>"
   name="form[<?=$field->name?>]"
   value="<?= $field->displayVar(); ?>" />
  <?= $field->htmlError(); ?>
</div>
