<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithFields;
use GDO\Core\GDT_Template;

final class GDT_Menu extends GDT
{
	use WithFields;
	
	public function render() { return GDT_Template::php('UI', 'cell/menu.php', ['field'=>$this]); }
}
