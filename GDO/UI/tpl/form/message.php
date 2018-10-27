<?php /** @var $field \GDO\UI\GDT_Message **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label for="form[<?= $field->name; ?>]"><?= $field->displayLabel(); ?></label>
  <?= $field->htmlIcon(); ?>
  <textarea
   class="<?=$field->classEditor()?>"
   name="form[<?= $field->name; ?>]"
   rows="6"
   maxRows="6"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>><?= $field->displayVar(); ?></textarea>
  <?= $field->htmlError(); ?>
</div>
