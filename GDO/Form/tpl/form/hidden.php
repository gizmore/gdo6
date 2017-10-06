<?php
use GDO\Form\GDT_Hidden;
$field instanceof GDT_Hidden;
?>
<input
 class="n"
 name="form[<?= $field->name?>]"
 value="<?= $field->displayVar(); ?>"
 type="hidden" />
