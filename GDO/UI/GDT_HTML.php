<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Very simple field that only has custom html content.
 * 
 * @author gizmore
 * @see \GDO\UI\GDT_Panel
 * @version 6.10
 * @since 6.07
 */
final class GDT_HTML extends GDT
{
	use WithHTML;
	
	##############
	### Render ###
	##############
	public function render() { return $this->renderCell(); }
	public function renderCard() { return "<div class=\"gdt-html\">{$this->renderCell()}</div>"; }
	public function renderCell()
	{
	    return $this->html;
	}

}
