<?php /** @var $field \GDO\DB\GDT_String **/ ?>
<input
 name="f[<?=$field->name?>]"
 type="search"
 size="<?=min($field->max, 16)?>"
 value="<?=html($field->filterValue())?>"
 placeholder="<?=t('string_filter')?>" />
