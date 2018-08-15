<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
/**
 * A horizontal divider.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class GDT_Divider extends GDT
{
	use WithLabel;
	use WithIcon;
	
	public function defaultLabel() { return $this->noLabel(); }
	
	public function renderCell() { return GDT_Template::php('UI', 'cell/divider.php', ['field' => $this]); }
	public function renderForm() { return GDT_Template::php('UI', 'cell/divider.php', ['field' => $this]); }
}
