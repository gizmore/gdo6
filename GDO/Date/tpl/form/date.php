<?php /** @var $field \GDO\Date\GDT_Date **/ ?>
<div class="gdt-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="date"
   <?=$field->htmlFormName()?>
   <?=$field->htmlValue()?>
   <?=$field->htmlDisabled()?> />
  <?=$field->htmlError()?>
</div>
