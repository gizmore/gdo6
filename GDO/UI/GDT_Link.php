<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\DB\GDT_String;

/**
 * An anchor for menus or paragraphs.
 * 
 * @author gizmore
 * @since 6.00
 * @version 6.06
 */
class GDT_Link extends GDT_String
{
	use WithIcon;
	use WithLabel;
	use WithHREF;
	use WithHTML;
	use WithPHPJQuery;
	
	public $caseSensitive = true;
	public function isSerializable() { return false; }
	
	/**
	 * Output a link / anchor.
	 * @deprecated
	 * @param string $href
	 * @param string $label
	 * @return string
	 */
	public static function anchor($href, $label=null)
	{
		$label = $label !== null ? $label : $href;
		return self::make()->href($href)->rawLabel($label)->render();
	}
	
	##############
	### Render ###
	##############
	public function renderCell() { return GDT_Template::php('UI', 'cell/link.php', ['link' => $this]); }
	public function renderForm() { return $this->renderCell(); }
	
	###################
	### Link target ###
	###################
	private $target;
	public function target($target) { $this->target = $target; return $this; }
	public function targetBlank() { return $this->target('__blank'); }
	public function htmlTarget() { return $this->target === null ? '' : " target=\"{$this->target}\""; }
	
}
