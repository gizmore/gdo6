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
if ($field->quicksort && $field->headers && $field->headers->fieldCount())
{
    $haveSearch = $haveOrder = false;
    
    $formSearch = GDT_Form::make($field->headers->name)->slim()->methodGET();
    if ($field->searchable && $field->headers->searchableFieldCount())
    {
        $haveSearch = 1;
        $formSearch->addField(GDT_SearchField::make('search'));
    }

##################
### Order Form ###
##################
    if ($field->orderable && $field->headers && $field->headers->orderableFieldCount())
    {
        $haveOrder = 1;
        $formOrder = $formSearch;
        $select = GDT_Select::make('order_by')->icon('arrow_up');
        foreach ($field->headers->fields as $gdt)
        {
            if ($gdt->orderable)
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
    
    if ($haveSearch || $haveOrder)
    {
        $formSearch->addField(GDT_Submit::make('btn_search'));
        $bar = GDT_Bar::make()->horizontal();
        $bar->addFields([$formSearch]);
        echo $bar->render();
    }
}

############
### List ###
############
$pagemenu = $field->getPageMenu();
$pagemenu = $pagemenu ? $pagemenu->renderCell() : '';

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
