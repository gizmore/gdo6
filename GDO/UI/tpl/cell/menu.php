<?php /** @var $field \GDO\UI\GDT_Menu **/ ?>
<div class="gdt-menu">
<?php foreach ($field->getFields() as $gdoType) : ?>
  <?=$gdoType->render()?>
<?php endforeach; ?>
</div>
