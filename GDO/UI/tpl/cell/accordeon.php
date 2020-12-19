<?php
use GDO\UI\GDT_Icon;

/** @var \GDO\UI\GDT_Accordeon $field **/ ?>
<div class="gdo-panel gdt-accordeon <?=$field->opened?'opened':'closed'?>">
  <div class="title collapse-bar"> <?=GDT_Icon::iconS('plus')?> <?=$field->renderTitle()?></div>
  <div class="title uncollapse-bar"> <?=GDT_Icon::iconS('minus')?> <?=$field->renderTitle()?></div>
  <div class="collapse-content">
<?php foreach ($field->getFields() as $gdt) : ?>
<?php echo $gdt->renderCell(); ?>
<?php endforeach; ?>
  </div>
</div>
