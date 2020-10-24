<?php /** @var $field \GDO\DB\GDT_Decimal **/ ?>
<div class="gdo-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="number"
   <?=$field->htmlFormName()?>
   <?=$field->htmlDisabled()?>
   <?=$field->htmlRequired()?>
   min="<?=$field->min?>"
   max="<?=$field->max?>"
   step="<?=$field->step?>"
   value="<?=$field->getVar()?>" /><?= $field->htmlError(); ?></div>
