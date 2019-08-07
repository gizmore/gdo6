<?php
namespace GDO\UI;
use GDO\Core\GDT;
/**
 * Just a single icon.
 * @see WithIcon
 * @author gizmore
 * @since 6.00
 * @version 6.10
 */
class GDT_Icon extends GDT
{
	use WithIcon;

// 	/**
// 	 * Default icon size.
// 	 * @var integer
// 	 */
// 	const DEFAULT_SIZE = 14;
	
	/**
	 * When an icon provider is loaded, it changes this var.
	 * @var callable
	 */
	public static $iconProvider = ['GDO\UI\GDT_IconUTF8', 'iconS'];
	
// 	public $iconSize = self::DEFAULT_SIZE;
// 	public function iconSize($iconSize)
// 	{
// 		$this->iconSize = $iconSize;
// 		return $this;
// 	}
	
// 	public $iconImage = null;
// 	public function iconImage($iconImage)
// 	{
// 		$this->iconImage = $iconImage;
// 		return $this;
// 	}
	
// 	public $iconText = null;
// 	public function iconText($iconText)
// 	{
// 		$this->iconText = $iconText;
// 		return $this;
// 	}
	
// 	public function icon($iconImage, $iconText=null, $iconSize=0)
// 	{
// 		$this->iconImage = $iconImage;
// 		$this->iconText= $iconText;
// 		if ($iconSize) $this->iconSize($iconSize);
// 		return $this;
// 	}
	
// 	##############
// 	### Render ###
// 	##############
// 	public function htmlIcon()
// 	{
		
// 	}
	
}
