<?php /** @var $field \GDO\DB\GDT_Int **/ ?>
<input
 name="f[<?= $field->name; ?>]"
 type="text"
 pattern="^[-0-9]*$"
 value="<?= html($field->filterValue()); ?>"
 placeholder="n,n-m"
 size="1" />
