<?php
use GDO\UI\GDT_Button;
/** @var $field GDT_Button **/
$field instanceof GDT_Button;
$field->addClass('gdt-button');
?>
<?php
/** @var $href string **/
if ($href) : ?>
<a <?=$field->htmlAttributes()?>
 <?=$field->htmlDisabled()?>
 <?=$field->htmlRel()?>
 href="<?=html($href)?>">
  <?=$field->htmlIcon()?>
  <?=$field->displayLabel()?>
</a>
<?php endif; ?>
