<?php
use GDO\Core\GDT;
use GDO\UI\GDT_Icon;
$field instanceof GDT;
?>
<div class="gdo-tblorder">
  <a href="<?= $url_asc; ?>" class="asc<?= $is_asc ? ' sel' : ''; ?>"><?=GDT_Icon::iconS('arrow_drop_up');?></a>
  <a href="<?= $url_desc; ?>" class="desc<?= $is_desc ? ' sel' : ''; ?>"><?=GDT_Icon::iconS('arrow_drop_down');?></a>
</div>
