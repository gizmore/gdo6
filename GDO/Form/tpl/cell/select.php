<?php /** @var $field \GDO\Form\GDT_Select **/ ?>
  <select
<?php echo $field->htmlAttributes(); ?>
<?php if ($field->multiple) : ?>
   name="form[<?= $field->name?>][]"
   multiple="multiple"
   size="8"
<?php else : ?>
   name="form[<?= $field->name?>]"
<?php endif; ?>
   <?= $field->htmlDisabled(); ?>>
<?php if ($field->emptyLabel) : ?>
	<option value="<?=$field->emptyValue?>"<?=$field->htmlSelected($field->emptyValue)?>><?=$field->emptyLabel?></option>
<?php endif; ?>
<?php foreach ($field->choices as $value => $choice) : ?>
	<option value="<?=html($value)?>"<?=$field->htmlSelected($value);?>><?=$field->renderChoice($choice)?></option>
<?php endforeach; ?>
  </select>
