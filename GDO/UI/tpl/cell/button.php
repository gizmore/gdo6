<?php
use GDO\UI\GDT_Button;
$field instanceof GDT_Button;
?>
<?php if ($href) : ?>
<a class="md-button md-primary md-raised" href="<?= $href; ?>" <?= $field->htmlDisabled(); ?>>
  <?= $field->displayLabel(); ?>
  <?= $field->htmlIcon(); ?>
</a>
<?php endif; ?>
