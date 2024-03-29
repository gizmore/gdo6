<?php
namespace GDO\Mail\tpl\form;
use GDO\Mail\GDT_Email;
/** @var $field GDT_Email **/
?>
<div class="gdt-container<?= $field->classError(); ?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?= $field->displayLabel(); ?></label>
  <input
   size="32"
   type="email"
   <?=$field->htmlID()?>
   <?=$field->htmlFormName()?>
   value="<?= $field->display(); ?>"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?> />
  <?= $field->htmlError(); ?>
</div>
