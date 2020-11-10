<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithFields;
use GDO\Core\GDT_Template;

/**
 * Popup menu
 * @author gizmore
 * @version 6.10
 * @since 6.04
 */
final class GDT_Menu extends GDT
{
	use WithFields;
	use WithLabel;
	
	public function renderCell() { return GDT_Template::php('UI', 'cell/menu.php', ['field'=>$this]); }
	
}
