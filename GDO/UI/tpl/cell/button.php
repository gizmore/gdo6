<?php
use GDO\UI\GDT_Button;
$field instanceof GDT_Button;
$field->addClass('gdt-button');
?>
<?php if ($href) : ?>
<a <?=$field->htmlAttributes()?> href="<?=$href?>" <?=$field->htmlDisabled()?>>
  <?=$field->htmlIcon()?>
  <?=$field->displayLabel()?>
</a>
<?php endif; ?>
