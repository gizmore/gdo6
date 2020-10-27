<?php /** @var $field \GDO\DB\GDT_Enum **/
$sel = ' selected="selected"';
$var = $field->getVar();
?>
<div class="gdo-container<?=$field->classError()?>">
  <?= $field->htmlIcon(); ?>
  <label><?=$field->displayLabel()?></label>
  <select
<?php if ($field->completionHref) : ?>
   class="gdo-autocomplete-enum"
   data-config='<?=$field->displayConfigJSON()?>'
<?php endif; ?>
   <?=$field->htmlFormName()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlDisabled()?>>
<?php if ($field->emptyLabel) : ?>
	  <option value="<?=$field->emptyValue?>"<?=$field->emptyValue === $var ? $sel : ''?>><?=$field->displayEmptyLabel()?></option>
<?php endif; ?>
	<?php foreach ($field->enumValues as $enumValue) : ?>
	  <option value="<?= $enumValue; ?>"<?= $enumValue === $var ? $sel : ''; ?>><?=$field->enumLabel($enumValue)?></option>
	<?php endforeach; ?>
  </select>
  <?=$field->htmlError()?>
</div>
