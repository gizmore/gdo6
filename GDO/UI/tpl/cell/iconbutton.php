<?php
/** @var $field \GDO\UI\GDT_IconButton **/
/** @var $href string **/
?>
<?php $field->addClass('gdt-iconbutton'); ?>
<a
 href="<?= $href; ?>"
<?php if (!$field->writable) : ?>
 onclick="return false;"
<?php endif; ?>
 <?=$field->htmlDisabled()?>
 <?=$field->htmlAttributes()?>
  ><?=$field->htmlIcon()?>
  <?php if ($field->label || $field->labelRaw) : ?>
    <?=$field->displayLabel()?>
  <?php endif; ?>
</a>
