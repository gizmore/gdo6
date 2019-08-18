<?php
use GDO\UI\GDT_Image;
/**
 * @var $field GDT_Image
 */
$field instanceof GDT_Image;
?>
<img
 name="<?=$field->name?>"
 class="gdt-image"
 src="<?=html($field->src)?>"
 <?=$field->htmlAttributes()?> />
