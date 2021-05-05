<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Very simple field that only has custom html content.
 * 
 * @see \GDO\UI\GDT_Panel
 * 
 * @author gizmore
 * 
 * @version 6.10.1
 * @since 6.7.0
 */
final class GDT_HTML extends GDT
{
	use WithHTML;
	
	##############
	### Render ###
	##############
	public function render()
	{
	    return $this->renderCell();
	}
	public function renderCard()
	{
	    return "<div class=\"gdt-html\">{$this->renderCell()}</div>";
	}
	
	public function renderCell()
	{
	    return $this->html;
	}

}
