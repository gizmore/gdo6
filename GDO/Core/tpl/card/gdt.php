<?php
use GDO\Core\GDT;
/** @var $gdt GDT **/
$gdt instanceof GDT;

?>
<div>
  <label><?=$gdt->displayLabel()?>:</label>
  <span><?=$gdt->renderCell()?></span>
</div>
