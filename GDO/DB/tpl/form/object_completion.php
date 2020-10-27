<?php
/** @var $field \GDO\DB\GDT_Object **/
?>
<div class="gdo-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   style="width: 330px;"
   class="gdo-autocomplete"
   data-config='<?=$field->displayConfigJSON()?>'
   <?=$field->htmlID()?>
   type="text"
   <?=$field->htmlRequired()?>
   <?=$field->htmlDisabled()?>
   <?=$field->htmlFormName()?>
   value="<?=$field->displayVar()?>" />
  <input type="hidden" name="nocompletion_<?=$field->name?>" value="1" />
  <input type="hidden" id="completion-<?=$field->name?>" value="<?=$field->displayVar()?>" />
  <?=$field->htmlError()?>
</div>
