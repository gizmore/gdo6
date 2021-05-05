<?php /** @var $field \GDO\Core\GDT **/
use GDO\UI\GDT_Icon;
/** @var $is_asc boolean **/
/** @var $url_asc string **/
/** @var $is_desc boolean **/
/** @var $url_desc string **/
?>
<div class="gdt-tblorder">
  <a href="<?=$url_asc?>" rel="nofollow" class="asc<?=$is_asc?' sel':''?>"><?=GDT_Icon::iconS('arrow_up')?></a>
  <a href="<?=$url_desc?>" rel="nofollow" class="desc<?=$is_desc?' sel':''?>"><?=GDT_Icon::iconS('arrow_down')?></a>
</div>
