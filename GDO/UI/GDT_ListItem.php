<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Avatar\GDO_Avatar;
use GDO\User\GDO_User;

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
	
	public $avatar;
	public function avatar($avatar) { $this->avatar = $avatar; return $this; }
	
	public function userAvatar(GDO_User $user) { return $this->avatar(GDO_Avatar::forUser($user)); }
	
	
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
