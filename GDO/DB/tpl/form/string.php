<?php /** @var $field \GDO\DB\GDT_String **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   type="<?=$field->_inputType?>"
   <?=$field->htmlID()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlPattern()?>
   <?=$field->htmlDisabled()?>
   minlength="<?=$field->min?>"
   maxlength="<?=$field->max?>"
   size="<?=min($field->max, 32) ?>"
   <?=$field->htmlFormName()?>
   value="<?= $field->displayVar(); ?>" />
  <?= $field->htmlError(); ?>
</div>
