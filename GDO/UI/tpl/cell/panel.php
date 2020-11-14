<?php
/** @var \GDO\UI\GDT_Panel $field **/ ?>
<div class="gdo-panel">
<?php if ($field->hasTitle()) : ?>
  <h3><?=$field->renderTitle()?></h3>
<?php endif; ?>
<?php if ($field->hasText()) : ?>
  <p><?=$field->renderText()?></p>
<?php endif; ?>
<?php if ($field->fields) : ?>
<?php foreach ($field->fields as $gdt) : ?>
<?=$gdt->renderCell()?>
<?php endforeach; ?>
<?php endif; ?>
</div>
