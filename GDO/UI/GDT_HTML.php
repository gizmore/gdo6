<?php
namespace GDO\UI;
use GDO\Core\GDT;
/**
 * Very simple field that only has custom html content.
 * @author gizmore
 * @see \GDO\UI\GDT_Panel
 */
final class GDT_HTML extends GDT
{
	use WithHTML;
	public function render() { return $this->html; }
}
