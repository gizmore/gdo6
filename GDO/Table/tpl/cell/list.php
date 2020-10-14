<?php
use GDO\Table\GDT_List;
use GDO\Form\GDT_Form;
use GDO\UI\GDT_SearchField;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Select;
use GDO\UI\GDT_Bar;

/** @var $field GDT_List **/

###################
### Search Form ###
###################
$formSearch = GDT_Form::make('s')->slim()->methodGET();
if ($field->searchable)
{
    $formSearch->addField(GDT_SearchField::make('search'));
}

##################
### Order Form ###
##################
if ($field->orderableField)
{
    $formOrder = $formSearch; # GDT_Form::make('o')->slim()->methodGET();
    $select = GDT_Select::make('order_by');
    foreach ($field->headers->fields as $gdt)
    {
        if ($gdt->orderableField)
        {
            $select->choices[$gdt->name] = $gdt->displayLabel();
        }
    }
    $select->initial($field->orderDefault);
    $formOrder->addField($select);
    
    $ascdesc = GDT_Select::make('order_dir');
    $ascdesc->choices['ASC'] = t('asc');
    $ascdesc->choices['DESC'] = t('desc');
    $ascdesc->initial($field->orderDefaultAsc ? 'ASC' : 'DESC');
    $formOrder->addField($ascdesc);
}

if ($field->searchable || $field->orderableField)
{
    $formSearch->addField(GDT_Submit::make('btn_search'));
    $bar = GDT_Bar::make()->horizontal();
    $bar->addFields([$formSearch]);
    echo $bar->render();
}

############
### List ###
############
$pagemenu = $field->getPageMenu();
$pagemenu = $pagemenu ? $pagemenu->render() : '';

$result = $field->getResult();
$template = $field->getItemTemplate();

echo $pagemenu;
?>
<!-- Begin List -->
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
<!-- End of List -->
<?php
echo $pagemenu;
