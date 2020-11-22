<?php
/** @var \GDO\UI\GDT_Accordeon $field **/ ?>
<div class="gdo-panel gdt-accordeon">
<?php if ($field->hasTitle()) : ?>
  <div class="title"><?=$field->renderTitle()?></div>
<?php endif; ?>
<?php foreach ($field->getFields() as $gdt) : ?>
<?php echo $gdt->renderCell(); ?>
<?php endforeach; ?>
</div>
