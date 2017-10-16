<?php /** @var $field \GDO\DB\GDT_String **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <?= $field->htmlIcon(); ?>
  <input
   type="text"
   <?=$field->htmlID()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlPattern()?>
   <?=$field->htmlDisabled()?>
   min="<?=$field->min?>"
   max="<?=$field->max?>"
   size="<?=min($field->max, 64) ?>"
   name="form[<?=$field->name?>]"
   value="<?= $field->displayVar(); ?>" />
  <?= $field->htmlError(); ?>
</div>
