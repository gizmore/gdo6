<?php /** @var $field \GDO\Form\GDT_Select **/ ?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label><?= $field->label; ?></label><?=$field->htmlIcon()?>
  <select
<?php if ($field->multiple) : ?>
   name="form[<?= $field->name?>][]"
   multiple="multiple"
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
  <?= $field->htmlError(); ?>
</div>
