<?php /** @var $field \GDO\DB\GDT_Int **/ ?>
<input
 name="f[<?= $field->filterField ? $field->filterField : $field->name; ?>]"
 type="text"
 value="<?= html($field->filterValue()); ?>"
 size="5" />
