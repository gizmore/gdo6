<?php /** @var $field \GDO\Date\GDT_Date **/ ?>
<div class="gdo-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="date"
   <?=$field->htmlFormName()?>
   value="<?=$field->getVar()?>"
   <?=$field->htmlDisabled()?> />
  <?=$field->htmlError()?>
</div>
