<?php /** @var $field \GDO\UI\GDT_Textarea **/ ?>
<div class="gdt-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->displayLabel()?></label>
  <textarea
   <?=$field->htmlID()?>
   class="<?=$field->classEditor()?>"
   <?=$field->htmlFormName()?>
   rows="6"
   <?=$field->htmlRequired()?>
   <?=$field->htmlDisabled()?>><?=html($field->getVar())?></textarea>
  <?=$field->htmlError()?>
</div>
