<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
/**
 * @author gizmore
 * @since 6.00
 * @version 6.10
 */
class GDT_Badge extends GDT
{
	use WithLabel;
	use WithPHPJQuery;
	
	public $badge;
	public function badge($badge) { $this->badge = $badge; return $this; }
	
	public function renderCell() { return GDT_Template::php('UI', 'cell/badge.php', ['field' => $this]); }
}
