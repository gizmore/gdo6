<?php
use GDO\Table\GDT_List;
use GDO\UI\GDT_Icon;
use GDO\Util\Common;
$field instanceof GDT_List;

$pagemenu = $field->getPageMenu();
$result = $field->getResult();
$template = $field->getItemTemplate();
echo $pagemenu ? $pagemenu->renderCell() : null;
?>
<!-- List -->
<ul class="gdt-list">
<?php if ($field->title) : ?>
  <h3><?=$field->title?></h3>
<?php endif; ?>
<?php
while ($gdo = $result->fetchObject()) :
	echo $template->gdo($gdo)->renderList();
endwhile;
?>
</ul>
<?php
echo $pagemenu ? $pagemenu->renderCell() : null;
