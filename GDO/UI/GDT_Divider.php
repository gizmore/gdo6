<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A horizontal divider tag.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.0.0
 */
class GDT_Divider extends GDT
{
	use WithLabel;

	public $editable = false;

	public function blankData() {}

	public function defaultLabel() { return $this->noLabel(); }

	public function renderJSON() {}
	public function renderCLI() {}
	public function renderCell() { return GDT_Template::php('UI', 'cell/divider.php', ['field' => $this]); }
	public function renderForm() { return GDT_Template::php('UI', 'cell/divider.php', ['field' => $this]); }
	public function renderCard()
	{
	    return '<div class="gdt-card-divider">'.$this->displayLabel().'</div>';
	}

}
