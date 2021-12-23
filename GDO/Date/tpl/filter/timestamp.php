<?php
/** @var $field \GDO\Date\GDT_Timestamp **/
/** @var $f string **/
?>
<input
 name="<?=$f?>[f][<?=$field->name?>][min]"
 type="search"
 pattern="^[-\.0-9/ :aAmMpP]*$"
 value="<?=$field->displayVar(@$field->filterVar($f)['min'])?>"
 placeholder="<?=t('from')?>" />
<input
 name="<?=$f?>[f][<?=$field->name?>][max]"
 type="search"
 pattern="^[-\.0-9/ :aAmMpP]*$"
 value="<?=$field->displayVar(@$field->filterVar($f)['max'])?>"
 placeholder="<?=t('to')?>" />
 