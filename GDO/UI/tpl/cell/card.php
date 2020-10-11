<?php
use GDO\Profile\GDT_ProfileLink;
use GDO\Date\Time;
/** @var $field \GDO\UI\GDT_Card **/
?>
<?php if ($field->gdo) : ?>
<a name="card-<?=$field->gdo->getID()?>" class="dn"></a>
<?php endif; ?>
<div class="gdo-card">
<?php if ($field->hasUpperCard()) : ?>
  <div class="card-upper">
<?php if ($field->withCreator) : ?>
	<div class="card-creator"><?=GDT_ProfileLink::make()->forUser($field->gdoCreator())->avatarSize(42)->render()?></div>
<?php endif; ?>
	<div class="card-titles">
<?php if ($field->title) : ?>
	  <div class="card-title"><?=$field->title?></div>
<?php endif; ?>
<?php if ($field->subtitle) : ?>
	  <div class="card-subtitle"><?=$field->subtitle?></div>
<?php endif; ?>
<?php if ($field->withCreated) : ?>
      <?php $date = $field->gdoCreated(); ?>
	  <div class="card-created"><?=Time::displayDate($date) . ' - ' . Time::displayAge($date)?></div>
<?php endif; ?>
	</div>
	<div class="cb"></div>
  </div>
<?php endif; ?>
  
  <div class="card-content">
<?php foreach ($field->getFields() as $gdt) : ?>
	<?=$gdt->renderCard()?>
<?php endforeach; ?>
  </div>
  
<?php if ($field->hasActions()) : ?>
  <div class="card-actions">
	<?=$field->actions()->render()?>
  </div>
<?php endif; ?>
  
</div>
