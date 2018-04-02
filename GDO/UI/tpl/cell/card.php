<?php /** @var $field \GDO\UI\GDT_Card **/
use GDO\Profile\GDT_ProfileLink;
?>
<?php if ($field->gdo) : ?>
<a name="card-<?=$field->gdo->getID()?>"></a>
<?php endif; ?>
<div class="gdo-card">
  <div class="card-upper">
<?php if ($field->withCreator) : ?>
    <div class="card-creator"><?=GDT_ProfileLink::make()->forUser($field->gdoCreator())->render()?></div>
<?php endif; ?>
    <div class="card-titles">
<?php if ($field->title) : ?>
      <div class="card-title"><?=$field->title?></div>
<?php endif; ?>
<?php if ($field->subtitle) : ?>
      <div class="card-subtitle"><?=$field->subtitle?></div>
<?php endif; ?>
    </div>
  </div>
  
  <div class="card-content">
<?php foreach ($field->getFields() as $gdt) : ?>
    <?=$gdt->render()?>
<?php endforeach; ?>
  </div>
  
  <div class="card-actions">
    <?=$field->actions()->render()?>
  </div>
</div>
