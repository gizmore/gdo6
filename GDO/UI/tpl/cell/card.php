<?php /** @var $field \GDO\UI\GDT_Card **/ ?>
<div class="gdt-card">
<?php if ($field->title) : ?>
  <div class="card-title"><?=$field->title?></div>
<?php endif; ?>
  <div class="card-content">
<?php foreach ($field->getFields() as $gdt) : ?>
    <?=$gdt->render()?>
<?php endforeach; ?>
  </div>
  <div class="card-actions">
    <?=$field->getActions()->render()?>
  </div>
</div>
