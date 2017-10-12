<?php /** @var $field \GDO\UI\GDT_IconButton **/ ?>
<?php if ($href) : ?>
<a href="<?= $href; ?>"
 title=""
 <?= $field->htmlDisabled(); ?>><?= html($field->label); ?><?= $field->htmlIcon(); ?></a>
<?php endif; ?>
