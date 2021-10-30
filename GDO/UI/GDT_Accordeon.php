<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A panel that collapses onclick.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.0
 */
final class GDT_Accordeon extends GDT_Panel
{
    public function renderCell()
    {
        return GDT_Template::php('UI', 'cell/accordeon.php', ['field' => $this]);
    }

    public function renderCard()
    {
        return '<label></label>' . $this->renderCell();
    }
    
    ##############
    ### Opened ###
    ##############
    public $opened = false;
    public function opened($opened=true)
    {
        $this->opened = $opened;
        return $this;
    }
    
    public function closed($closed=true)
    {
    	return $this->opened(!$closed);
    }
    
    /**
     * @var GDT[]
     */
    public $sections = [];
    public $titles = [];
    public function addSection($title, GDT $gdt)
    {
        $this->titles[] = $title;
        $this->sections[] = $gdt;
        return $this;
    }

}
