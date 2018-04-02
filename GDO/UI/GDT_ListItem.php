<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A list item.
 * Has a title, subtitle, subtext, image and menu.
 * @author gizmore
 * @since 6.07
 */
final class GDT_ListItem extends GDT
{
	use WithPHPJQuery;
	
	public $image;
	public function image($image) { $this->image = $image; return $this; }
	
	public $title;
	public function title($title) { $this->title = $title; return $this; }
	
	public $subtitle;
	public function subtitle($subtitle) { $this->subtitle = $subtitle; return $this; }
	
	public $subtext;
	public function subtext($subtext) { $this->subtext = $subtext; return $this; }
	
	/**
	 * @var GDT_Menu
	 */
	public $actions;
	public function actions()
	{
		if (!$this->actions)
		{
			$this->actions = GDT_Menu::make();
		}
		return $this->actions;
	}
	
	public function renderCell() { return GDT_Template::php('UI', 'cell/list_item.php', ['gdt'=>$this]); }
	
}
