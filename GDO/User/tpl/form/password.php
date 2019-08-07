<?php /** @var $field \GDO\User\GDT_Password **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?= $field->htmlTooltip(); ?>
  <?= $field->htmlIcon(); ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->displayLabel(); ?></label>
  <input
   type="password"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlPattern(); ?>
   min="<?= $field->min; ?>"
   max="<?= $field->max; ?>"
   size="<?= min($field->max, 32); ?>"
   name="form[<?= $field->name; ?>]"
   value="<?= $field->getVar(); ?>" />
  <?= $field->htmlError(); ?>
</div>
