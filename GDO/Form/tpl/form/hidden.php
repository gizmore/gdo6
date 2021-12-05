<?php /** @var $field \GDO\Form\GDT_Hidden **/ ?>
<input
 class="n"
 <?=$field->htmlFormName()?>
 value="<?= $field->display(); ?>"
 type="hidden" />
