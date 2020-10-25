<?php
/** @var $field \GDO\DB\GDT_Object **/
?>
<div class="gdo-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   class="gdo-autocomplete"
   data-config='<?=$field->displayConfigJSON()?>'
   <?=$field->htmlID()?>
   type="text"
   <?=$field->htmlRequired()?>
   <?=$field->htmlDisabled()?>
   <?=$field->htmlFormName()?>
   value="<?=$field->displayVar()?>" />
  <input type="hidden" name="nocompletion_<?=$field->name?>" value="1" />
  <?=$field->htmlError()?>
</div>
