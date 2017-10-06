<?php
use GDO\DB\GDT_Enum;
$field instanceof GDT_Enum;
$sel = 'selected="selected"';
$val = $field->getVar();
?>
<div class="gdo-container<?= $field->classError(); ?>">
  <label><?= $field->label; ?></label>
  <select
   name="form[<?=$field->name;?>]"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>>
<?php if ($field->emptyLabel) : ?>
      <option value="<?= $field->emptyValue; ?>" <?= $field->emptyValue === $val ? $sel : ''; ?>><?= t('enum_'.$enumValue); ?></option>
<?php endif; ?>
    <?php foreach ($field->enumValues as $enumValue) : ?>
      <option value="<?= $enumValue; ?>" <?= $enumValue === $val ? $sel : ''; ?>><?= t('enum_'.$enumValue); ?></option>
    <?php endforeach; ?>
  </select>
  <div class="gdo-error"><?= $field->error; ?></div>
</div>
