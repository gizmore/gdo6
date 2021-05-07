<?php /** @var $field \GDO\User\GDT_Password **/ ?>
<div class="gdt-container<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label <?=$field->htmlForID()?>><?= $field->displayLabel(); ?></label>
  <input
   <?=$field->htmlID()?>
   type="password"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlPattern(); ?>
   min="<?= $field->min; ?>"
   max="<?= $field->max; ?>"
   size="<?= min($field->max, 32); ?>"
   <?=$field->htmlFormName()?>
   value="" />
  <?= $field->htmlError(); ?>
</div>
