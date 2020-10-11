<?php /** @var $field \GDO\Date\GDT_DateTime **/
$id = 'date_'.$field->name; ?>
<div class="gdo-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label for="<?=$id?>"><?=$field->displayLabel()?></label>
  <input
   id="<?=$id?>"
   type="datetime"
   name="form[<?= $field->name; ?>]"
   value="<?=$field->displayVar()?>"
   <?=$field->htmlDisabled()?> />
  <?=$field->htmlError()?>
</div>
