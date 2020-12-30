<?php
use GDO\UI\GDT_Image;
/**
 * @var $field GDT_Image
 */
$field->addClass('gdt-image');
?>
<img
 <?=$field->htmlID()?>
 name="<?=$field->name?>"
 src="<?=html($field->src)?>"
 <?=$field->htmlAttributes()?> />
