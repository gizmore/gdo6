<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
/**
 * Simple content pane.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class GDT_Panel extends GDT
{
	use WithIcon;
	use WithHTML;
	use WithTitle;
	use WithPHPJQuery;
	use WithFields;
	
	
	public function isError() { return false; }

//	 public function render() { return $this->HTML; }
	public function renderCell() { return GDT_Template::php('UI', 'cell/panel.php', ['field' => $this]); }
}
