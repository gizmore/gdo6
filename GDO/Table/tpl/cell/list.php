<?php
use GDO\Table\GDT_List;
use GDO\UI\GDT_SearchField;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;

/** @var $field GDT_List **/

echo GDT_Template::php('Table', 'cell/_listfilter.php', ['field' => $field]);

###################
### Search Form ###
###################
if ($field->searched)
{
	$formSearch = GDT_Form::make($field->headers->name)->slim()->methodGET();
	$formSearch->addField(GDT_SearchField::make('search'));
	$formSearch->actions()->addField(GDT_Submit::make()->css('display', 'none'));
	echo $formSearch->render();
}

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
$dummy = $field->fetchAs->cache->getDummy();
while ($gdo = $result->fetchInto($dummy)) :
	echo $template->gdo($gdo)->renderList();
endwhile;
?>
</div>
<!-- End of List -->
<?php
echo $pagemenu;
