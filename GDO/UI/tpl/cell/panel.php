<?php
/** @var \GDO\UI\GDT_Panel $field **/ ?>
<div class="gdo-panel"
><?= $field->html; ?>
<?php foreach ($field->getFields() as $gdt) : ?>
<?php echo $gdt->renderCell(); ?>
<?php endforeach; ?>
</div>
