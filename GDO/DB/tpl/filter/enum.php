<?php
use GDO\DB\GDT_Enum;
$field instanceof GDT_Enum;
$val = $field->filterValue();
$sel = 'selected="selected"';
?>
<select
 name="f[<?= $field->name?>][]"
 multiple="multiple">
<?php foreach ($field->enumValues as $enumValue) : ?>
  <option value="<?= $enumValue; ?>" <?= in_array($enumValue, $val, true) ? $sel : ''; ?>><?= t($enumValue); ?></option>
<?php endforeach; ?>
</select>
