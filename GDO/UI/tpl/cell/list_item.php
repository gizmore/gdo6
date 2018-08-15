<?php
/** @var $gdt \GDO\UI\GDT_ListItem **/
?>
<div class="gdt-list-item">
  <div class="gdt-li-upper">
<?php if ($gdt->image) : ?>
	<div class="gdt-li-image"><?=$gdt->image->renderCell()?></div>
<?php endif; ?>
	<div class="gdt-li-content">
	  <div class="gdt-li-title"><?=$gdt->title->renderCell()?></div>
	  <div class="gdt-li-subtitle"><?=$gdt->subtitle->renderCell()?></div>
	</div>
  </div>
  <div class="gdt-li-lower">
	<div class="gdt-li-subtext"><?=$gdt->subtext->renderCell()?></div>
  </div>
  <div class="gdt-li-actions"><?=$gdt->actions()->render()?></div>
</div>
