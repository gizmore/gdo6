<?php
use GDO\Form\GDT_Hidden;
$field instanceof GDT_Hidden;
?>
<input
 class="n"
 name="<?= $field->name?>"
 value="<?= $field->displayVar(); ?>"
 type="hidden" />
