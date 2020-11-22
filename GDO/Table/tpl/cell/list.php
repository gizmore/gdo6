<?php
use GDO\Table\GDT_List;
use GDO\Form\GDT_Form;
use GDO\UI\GDT_Accordeon;
use GDO\UI\GDT_SearchField;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Select;

/** @var $field GDT_List **/

###################
### Search Form ###
###################
if ($field->headers)
{
    # The list search criteria form.
    $frm = GDT_Form::make($field->headers->name)->slim()->methodGET();
    
    # Searchable input
    if ($field->searched)
    {
        $searchable = [];
        foreach ($field->headers->fields as $gdt)
        {
            if ($gdt->searchable)
            {
                $searchable[] = $gdt;
            }
        }
        if (count($searchable))
        {
            $frm->addField(GDT_SearchField::make('search'));
        }
    }
    
    # Orderable select
    if ($field->ordered)
    {
        $orderable = [];
        foreach ($field->headers->fields as $gdt)
        {
            if ($gdt->orderable)
            {
                if (!$gdt->hidden)
                {
                    $orderable[$gdt->name] = $gdt->displayLabel();
                }
            }
        }
        
        if (count($orderable))
        {
            $select = GDT_Select::make('order_by')->icon('arrow_up');
            $select->choices($orderable);
            $select->initial($field->orderDefault);
            $frm->addField($select);
            
            $ascdesc = GDT_Select::make('order_dir');
            $ascdesc->choices['ASC'] = t('asc');
            $ascdesc->choices['DESC'] = t('desc');
            $ascdesc->initial($field->orderDefaultAsc ? 'ASC' : 'DESC');
            $frm->addField($ascdesc);
        }
    }
    
    if ($field->filtered)
    {
        # Not supported yet
    }
    
    # Show quicksearch form in accordeon
    if (count($frm->fields))
    {
        $frm->addField(GDT_Submit::make());
        $accordeon = GDT_Accordeon::make()->addField($frm)->title($frm->displaySearchCriteria());
        echo $accordeon->renderCell();
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
