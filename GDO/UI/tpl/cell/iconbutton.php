<?php /** @var $field \GDO\UI\GDT_IconButton **/ ?>
<?php if ($href) : ?>
<a class="gdo-icon-button"
 href="<?= $href; ?>"
 title="<?= html($field->tooltip); ?>"
 <?= $field->htmlDisabled(); ?>><?= $field->label; ?>&nbsp;<?= $field->htmlIcon(); ?></a>
<?php endif; ?>
