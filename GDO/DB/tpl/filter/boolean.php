<?php /** @var $field \GDO\DB\GDT_Checkbox **/ ?>
<?php $val = $field->filterValue(); ?>
<select name="f[<?= $field->name ?>]" onchange="submit()">
  <option value="" <?= $val === '' ? 'selected="selected"' : ''; ?>><?=t('sel_all')?></option>
  <option value="1" <?= $val === '1' ? 'selected="selected"' : ''; ?>><?=t('sel_checked')?></option>
  <option value="0" <?= $val === '0' ? 'selected="selected"' : ''; ?>><?=t('sel_unchecked')?></option>
</select>
