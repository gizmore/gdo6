<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A simple paragraph.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.0.0
 */
class GDT_Paragraph extends GDT
{
    use WithText;

    public function render() { return $this->renderCell(); }
    public function renderCard() { return $this->renderCell(); }
	public function renderCell()
	{
	    return sprintf('<p class="gdt-paragraph">%s</p>',
	        $this->renderText()); 
	}

}
