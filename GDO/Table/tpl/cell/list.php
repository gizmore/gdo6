<?php
use GDO\Table\GDT_List;
use GDO\Core\GDT_Template;

/** @var $field GDT_List **/

echo GDT_Template::php('Table', 'cell/_listfilter.php', ['field' => $field]);

############
### List ###
############
$pagemenu = $field->getPageMenu();
$pagemenu = $pagemenu ? $pagemenu->renderCell() : '';

if (!$field->countItems())
{
    if ($field->hideEmpty)
    {
        return;
    }
}

$result = $field->getResult();
$template = $field->getItemTemplate();

echo $pagemenu;
?>
<!-- Begin List -->
<div class="gdt-list">
<?php if ($field->hasTitle()) : ?>
  <h3><?=$field->renderTitle()?></h3>
<?php endif; ?>
<?php
while ($gdo = $result->fetchObject()) :
	echo $template->gdo($gdo)->renderList();
endwhile;
?>
</div>
<!-- End of List -->
<?php
echo $pagemenu;
