<?php
use GDO\UI\GDT_IconButton;
$field instanceof GDT_IconButton;
?>
<?php if ($href) : ?>
<a
 href="<?= $href; ?>"
 title="<?= html($field->label); ?>"
 <?= $field->htmlDisabled(); ?>>
  <?= $field->htmlIcon(); ?>
</a>
<?php endif; ?>
