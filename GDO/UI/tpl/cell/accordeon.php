<?php
/** @var \GDO\UI\GDT_Accordeon $field **/ ?>
<div class="gdo-panel gdt-accordeon"
><div class="title"><?=html($field->title)?></div><?=$field->html?>
<?php foreach ($field->getFields() as $gdt) : ?>
<?php echo $gdt->renderCell(); ?>
<?php endforeach; ?>
</div>
