<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithFields;

/**
 * Very simple field that only has custom html content.
 * 
 * @see \GDO\UI\GDT_Panel
 * 
 * @author gizmore
 * 
 * @version 6.10.3
 * @since 6.7.0
 */
final class GDT_HTML extends GDT
{
	use WithHTML;
	use WithFields;
	
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
	
	public function renderJSON()
	{
	    return $this->html;
	}

}
