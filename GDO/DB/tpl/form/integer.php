<?php
/** @var $field \GDO\DB\GDT_UInt **/
?>
<div class="gdo-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="number"
   min="<?=$field->min;?>"
   max="<?=$field->max;?>"
   step="<?=$field->step?>"
   <?=$field->htmlFormName()?>
   <?=$field->htmlDisabled()?>
   <?=$field->htmlRequired()?>
   value="<?=$field->getVar()?>" />
  <?=$field->htmlError()?>
</div>
