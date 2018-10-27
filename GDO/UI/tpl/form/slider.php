<?php /** @var $field \GDO\UI\GDT_Slider **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->displayLabel(); ?></label>
<?php if (is_array($field->step)) : $var = $field->getVar(); ?>
  <select name="<?= $field->name; ?>">
<?php foreach ($field->step as $value => $choice) : ?>
	<?php $sel = $value === $var ? ' selected="selected"' : ''; ?>
	<option value="<?= $value ?>"<?=$sel;?>><?= $choice ?>&nbsp;(<?=$value?>)</option>
<?php endforeach; ?>
  </select>
<?php else : ?>
  <input
   type="range"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>
   min="<?= $field->min; ?>"
   max="<?= $field->max; ?>"
   step="<?= $field->step; ?>"
   name="form[<?= $field->name; ?>]"
   value="<?= $field->getVar(); ?>" />
 <?php endif; ?>
  <?= $field->htmlError(); ?>
</div>