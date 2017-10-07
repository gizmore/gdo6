<?php /** @var $field \GDO\Form\GDT_Hidden **/ ?>
<input
 class="n"
 name="form[<?= $field->name; ?>]"
 value="<?= $field->displayVar(); ?>"
 type="hidden" />
