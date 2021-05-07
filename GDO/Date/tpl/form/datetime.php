<?php /** @var $field \GDO\Date\GDT_DateTime **/
?>
<div class="gdt-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="datetime-local"
   autocomplete="off"
   value="<?=tt($field->getVar(), 'local')?>"
   <?=$field->htmlFormName()?>
   <?=$field->htmlDisabled()?> />
  <?=$field->htmlError()?>
</div>
