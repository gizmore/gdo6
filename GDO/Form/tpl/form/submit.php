<?php
use GDO\Form\GDT_Submit;
/** @var $field GDT_Submit **/
?>
<input
 type="submit"
 name="<?=$field->formName()?>"
 value="<?=$field->displayLabel()?>"
 <?=$field->htmlAttributes()?>
 <?=$field->htmlDisabled()?> />
