<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Just a single icon.
 * 
 * @see WithIcon
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
 */
class GDT_Icon extends GDT
{
	use WithIcon;
	use WithPHPJQuery;

	/**
	 * When an icon provider is loaded, it changes the $iconProvider.
	 * @var callable
	 */
	public static $iconProvider = [GDT_IconUTF8::class, 'iconS'];
	
	##############
	### Render ###
	##############
	public function renderCell() { return $this->htmlIcon(); }
	public function renderCLI() { return $this->icon; }
	public function renderJSON() {}
	
}
