<?php /** @var $field \GDO\DB\GDT_Int **/ ?>
<input
 name="<?=$f?>[f][<?= $field->filterField ? $field->filterField : $field->name; ?>]"
 type="text"
 value="<?=html($field->filterVar($f))?>"
 size="5"
 placeholder="<?=t('object_filter')?>"  />
