<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
use GDO\Core\GDT_Hook;

/**
 * A bar is a collection of fields that can be arranged either horizontally or vertically.
 * @author gizmore
 * @version 6.10
 * @since 6.05
 */
class GDT_Bar extends GDT
{
    use WithFlex;
	use WithFields;
	use WithPHPJQuery;

	public function renderCell() { return GDT_Template::php('UI', 'cell/bar.php', ['bar' => $this]); }
	public function renderCard() { return GDT_Template::php('UI', 'card/bar.php', ['bar' => $this]); }
	
	public function yieldHook($hookName)
	{
		GDT_Hook::callHook($hookName, $this);
		return $this->render();
	}

}
