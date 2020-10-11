<?php /** @var $field \GDO\Form\GDT_Select **/ ?>
  <select
   <?=$field->htmlAttributes()?>
   <?=$field->htmlFormName()?>
<?php if ($field->multiple) : ?>
   multiple="multiple"
   size="8"
<?php endif; ?>
   <?= $field->htmlDisabled(); ?>>
<?php if ($field->emptyLabel) : ?>
	<option value="<?=$field->emptyValue?>"<?=$field->htmlSelected($field->emptyValue)?>><?=$field->displayEmptyLabel()?></option>
<?php endif; ?>
<?php foreach ($field->choices as $value => $choice) : ?>
	<option value="<?=html($value)?>"<?=$field->htmlSelected($value);?>><?=$field->renderChoice($choice)?></option>
<?php endforeach; ?>
  </select>
