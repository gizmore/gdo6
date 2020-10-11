<?php /** @var $field \GDO\UI\GDT_Message **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label <?=$field->htmlForID()?>><?= $field->displayLabel(); ?></label>
  <?= $field->htmlIcon(); ?>
  <textarea
   <?=$field->htmlID()?>
   class="<?=$field->classEditor()?>"
   <?=$field->htmlFormName()?>
   rows="6"
   maxRows="6"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>><?= $field->displayVar(); ?></textarea>
  <?= $field->htmlError(); ?>
</div>
