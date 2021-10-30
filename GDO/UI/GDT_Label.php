<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A simple text label.
 * Currently only renders in JSON and Card.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
 */
class GDT_Label extends GDT
{
	use WithLabel;
	
	public function isSerializable() { return true; }
	
	public function renderCell()
	{
		return GDT_Template::php('UI', 'cell/label.php', ['field'=>$this]);
	}
	
	public function renderJSON()
	{
		return $this->displayLabel();
	}
	
	public function renderCard()
	{
	    return '<div class="gdt-card-label">' . $this->displayLabel() . '</div>';
	}

}
