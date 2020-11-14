<?php
use GDO\DB\GDT_Enum;
/** @var $field GDT_Enum **/
$val = $field->filterVar($f);
$sel = ' selected="selected"';
?>
<select
 name="<?=$f?>[f][<?=$field->name?>][]"
 multiple="multiple">
<?php foreach ($field->enumValues as $enumValue) : ?>
  <option value="<?=$enumValue?>"<?=in_array($enumValue, $val, true)?$sel:''?>><?=t($enumValue)?></option>
<?php endforeach; ?>
</select>
