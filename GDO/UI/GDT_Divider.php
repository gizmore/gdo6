<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A horizontal divider. HR tag.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
class GDT_Divider extends GDT
{
	use WithLabel;
	
	public $editable = false;
	
	public function blankData() {}

	public function isSerializable() { return false; }
	public function defaultLabel() { return $this->noLabel(); }
	
	public function renderJSON() {}
	public function renderCell() { return GDT_Template::php('UI', 'cell/divider.php', ['field' => $this]); }
	public function renderForm() { return GDT_Template::php('UI', 'cell/divider.php', ['field' => $this]); }
	public function renderCard()
	{
	    return '<div class="gdt-card-divider">'.$this->displayLabel().'</div>';
	}
	
}
