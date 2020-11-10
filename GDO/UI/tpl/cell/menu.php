<?php /** @var $field \GDO\UI\GDT_Menu **/ ?>
<div class="gdt-menu">
<?php if ($field->label || $field->labelRaw) : ?>
  <div class="menu-title"><?=$field->displayLabel()?></div>
<?php endif; ?>
<?php foreach ($field->getFields() as $gdoType) : ?>
  <?=$gdoType->render()?>
<?php endforeach; ?>
</div>
