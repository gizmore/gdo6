<?php /** @var $field \GDO\UI\GDT_Menu **/ ?>
<div class="gdt-menu">
<?php if ($field->hasLabel()) : ?>
  <div class="menu-title"><?=$field->displayLabel()?></div>
<?php endif; ?>
<?php foreach ($field->getFields() as $gdt) : ?>
  <?=$gdt->renderCell()?>
<?php endforeach; ?>
</div>
