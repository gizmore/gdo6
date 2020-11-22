<?php /** @var $field \GDO\DB\GDT_Int **/ ?>
<input
 name="<?=$f?>[f][<?=$field->name?>]"
 type="search"
 pattern="^[-\.0-9]*$"
 value="<?=html($field->filterVar($f))?>"
 placeholder="<?=t('int_filter')?>"
 size="<?=$field->bytes*2?>" />
