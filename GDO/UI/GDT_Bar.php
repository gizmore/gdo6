<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\WithFields;

/**
 * A bar is a collection of fields that can be arranged either horizontally or vertically.
 * @author gizmore
 * @version 6.10
 * @since 6.05
 */
class GDT_Bar extends GDT_Container
{
    use WithFlex;
	use WithFields;
	use WithPHPJQuery;

	public function renderForm() { return GDT_Template::php('UI', 'cell/bar.php', ['bar' => $this, 'form' => true]); }
	public function renderCell() { return GDT_Template::php('UI', 'cell/bar.php', ['bar' => $this, 'form' => false]); }
	public function renderCard() { return $this->renderCell(); }
	
	public $wrap = true;
	public function wrap($wrap=true) { $this->wrap = $wrap; return $this; }
	
}
