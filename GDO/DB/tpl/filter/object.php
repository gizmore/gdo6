<?php /** @var $field \GDO\DB\GDT_Int **/ ?>
<input
 name="f[<?= $field->name; ?>]"
 type="text"
 value="<?= html($field->filterValue()); ?>"
 size="5" />
