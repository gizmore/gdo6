<?php /** @var $field \GDO\Form\GDT_ComboBox **/ ?>
<div class="gdt-container gdo-completion<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <input
   class="gdo-autocomplete-input"
   data-config='<?=$field->displayConfigJSON()?>'
   type="<?=$field->_inputType?>"
   <?=$field->htmlID()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlPattern()?>
   <?=$field->htmlDisabled()?>
   min="<?=$field->min?>"
   max="<?=$field->max?>"
   size="<?=min($field->max, 32)?>"
   <?=$field->htmlFormName()?>
   <?=$field->htmlPlaceholder()?>
   value="<?=$field->display()?>" />
  <?=$field->htmlError()?>
  <input type="hidden" id="completion-<?=$field->name?>" value="<?=$field->display()?>" />
</div>
