<?php
/** @var $gdt \GDO\UI\GDT_ListItem **/
?>
<div class="gdt-list-item">
  <div class="gdt-li-upper">
<?php if ($gdt->image) : ?>
	<div class="gdt-li-image"><?=$gdt->image->renderCell()?></div>
<?php endif; ?>
	<div class="gdt-li-content">
<?php if ($gdt->title) : ?>
	  <div class="gdt-li-title"><?=$gdt->title->renderCell()?></div>
<?php endif; ?>
<?php if ($gdt->subtitle) : ?>
	  <div class="gdt-li-subtitle"><?=$gdt->subtitle->renderCell()?></div>
<?php endif; ?>
	</div>
  </div>
  <div class="gdt-li-lower">
<?php if ($gdt->subtext) : ?>
	<div class="gdt-li-subtext"><?=$gdt->subtext->renderCell()?></div>
<?php endif; ?>
  </div>
  <div class="gdt-li-actions"><?=$gdt->actions()->render()?></div>
</div>
