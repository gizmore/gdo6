<?php
use GDO\Mail\GDT_Email;
/** @var $field GDT_Email **/
?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?= $field->displayLabel(); ?></label>
  <input
   size="32"
   type="email"
   <?=$field->htmlID()?>
   <?=$field->htmlFormName()?>
   value="<?= $field->displayVar(); ?>"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?> />
  <?= $field->htmlError(); ?>
</div>
