<?php /** @var $field \GDO\UI\GDT_IconButton **/ ?>
<?php if ($href) : ?>
<a class="gdo-icon-button"
 href="<?= $href; ?>"
 <?=$field->htmlDisabled()?>
 <?=$field->htmlAttributes()?>>
  <?= $field->htmlIcon(); ?>
  <?php if ($field->label || $field->labelRaw) : ?>
    &nbsp;<?= $field->displayLabel(); ?>
  <?php endif; ?>
</a>
<?php endif; ?>
