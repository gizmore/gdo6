<?php
namespace GDO\UI;
/**
 * Adds icon handling to a GDT.
 * The templates have to echo $field->htmlIcon() to render them.
 * 
 * Icons are rendered by the icon provider function stored in GDT_Icon via an icon name and size.
 * Also raw markup can be used instead of an icon name, which is then wrapped in a font-size span.
 * Color only works with markup where css colors could apply, e.g: Fonts or SVG drawings.
 * 
 * @example echo GDT_Icon::iconS('clock', 16, '#f00');
 * @example echo GDT_Icon::make()->rawIcon($site->getIconImage())->size(20)->render();
 * 
 * @author gizmore
 * @see \GDO\UI\GDT_Icon - for a standalone icon that is a gdt.
 * @version 6.05
 */
trait WithIcon
{
	###########################
	### Icon-Markup Factory ###
	###########################
	public static function iconS($icon, $iconText=null, $size=null, $color=null)
	{
		$style = self::iconStyle($size, $color);
		return call_user_func(GDT_Icon::$iconProvider, $icon, $iconText, $style);
	}
	
	public static function rawIconS($icon, $iconText=null, $size=null, $color=null)
	{
		if ($icon)
		{
			$style = self::iconStyle($size, $color);
			return sprintf('<i class="gdo-icon" title="%s"%s>%s</i>', html($iconText), $style, $icon);
		}
	}
	
	private static function iconStyle($size, $color)
	{
		$size = $size === null ? '' : "font-size:{$size}px;";
		$color = $color === null ? '' : "color:$color;";
		return ($color || $size) ? "style=\"$color$size\"" : '';
	}
	
	############
	### Icon ###
	############
	protected $icon;
	public function icon($icon) { $this->icon = $icon; return $this; }
	
	public $iconText;
	public function iconText($text) { $this->iconText = $text; return $this; }
	
	protected $rawIcon;
	public function rawIcon($rawIcon) { $this->rawIcon = $rawIcon; return $this; }

	protected $size;
	public function size($size) { $this->size = $size; return $this; }

	protected $color;
	public function color($color) { $this->color = $color; return $this; }
	
	##############
	### Render ###
	##############
	public function htmlIcon()
	{
		return $this->icon ?
			self::iconS($this->icon, $this->iconText, $this->size, $this->color) :
			self::rawIconS($this->rawIcon, $this->size, $this->color);
	}
}
