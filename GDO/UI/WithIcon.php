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
 * @see GDT_Icon - for a standalone icon that is a gdt.
 * @see GDT_IconUTF8 - for the minimal icon provider.
 * @version 6.10
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
	public $icon;
	public function icon($icon) { $this->icon = $icon; return $this; }
	
	public $iconText;
	public $iconTextArgs;
	public function iconText($text, $textArgs) { $this->iconText = $text; $this->iconTextArgs = $textArgs; return $this; }
	
	public $rawIcon;
	public function rawIcon($rawIcon) { $this->rawIcon = $rawIcon; return $this; }

	public $size;
	public function size($size) { $this->size = $size; return $this; }

	public $color;
	public function color($color) { $this->color = $color; return $this; }
	
	public function tooltip($text, $textArgs=null)
	{
	    if (!$this->icon)
	    {
	        $this->icon = 'help';
	    }
	    return $this->iconText($text, $textArgs);
	}
	
	##############
	### Render ###
	##############
	public function htmlIcon()
	{
	    $text = $this->iconText ? t($this->iconText, $this->iconTextArgs) : '';
	    $text = str_replace('"', "'", $text);
		return $this->icon ?
			self::iconS($this->icon, $text, $this->size, $this->color) :
			self::rawIconS($this->rawIcon, $text, $this->size, $this->color);
	}
}
