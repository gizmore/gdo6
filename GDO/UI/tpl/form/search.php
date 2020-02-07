<?php
use GDO\UI\GDT_SearchField;
/** @var $field GDT_SearchField **/
$field instanceof GDT_SearchField;
?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?=$field->htmlTooltip()?>
  <?= $field->htmlIcon(); ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->displayLabel(); ?></label>
  <input
   type="search"
   <?=$field->htmlID()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlPattern()?>
   <?=$field->htmlDisabled()?>
   min="<?=$field->min?>"
   max="<?=$field->max?>"
   size="<?=min($field->max, 32) ?>"
   name="form[<?=$field->name?>]"
   value="<?= $field->displayVar(); ?>" />
  <?= $field->htmlError(); ?>
</div>
