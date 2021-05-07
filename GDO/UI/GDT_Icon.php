<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Just a single icon.
 * 
 * @see WithIcon
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
class GDT_Icon extends GDT
{
	use WithIcon;
	use WithPHPJQuery;

	/**
	 * When an icon provider is loaded, it changes this var.
	 * @var callable
	 */
	public static $iconProvider = ['GDO\UI\GDT_IconUTF8', 'iconS'];
	
	public function renderCell()
	{
		return $this->htmlIcon();
	}
	
}
