<?php
/** @var $field \GDO\DB\GDT_String **/
/** @var $f string **/
?>
<input
 name="<?=$f?>[f][<?=$field->name?>]"
 type="search"
 size="<?=min($field->max, 16)?>"
 value="<?=html($field->filterVar($f))?>"
 placeholder="<?=t('string_filter')?>" />
