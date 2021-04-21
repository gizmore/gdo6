<?php /** @var $field \GDO\Date\GDT_DateTime **/
?>
<div class="gdo-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="datetime"
   <?=$field->htmlFormName()?>
   <?=$field->htmlValue()?>
   <?=$field->htmlDisabled()?> />
  <?=$field->htmlError()?>
</div>
