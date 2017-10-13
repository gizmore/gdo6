<?php /** @var $field \GDO\UI\GDT_IconButton **/ ?>
<?php if ($href) : ?>
<a class="gdo-icon-button"
 href="<?= $href; ?>"
 title="<?= html($field->label); ?>"
 <?= $field->htmlDisabled(); ?>><?= html($field->label); ?>&nbsp;<?= $field->htmlIcon(); ?></a>
<?php endif; ?>
