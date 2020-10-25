<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\DB\GDT_CreatedBy;
use GDO\DB\GDT_CreatedAt;
use GDO\Avatar\GDT_Avatar;
use GDO\Profile\GDT_ProfileLink;

/**
 * A list item.
 * Has a title, subtitle, subtext, image and menu.
 * @author gizmore
 * @version 6.10
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
	
	public function avatar($avatar) { return $this->image($avatar); }
	public function userAvatar(GDO_User $user) { return $this->avatar(GDT_Avatar::make()->user($user)); }
	
	/**
	 * Use the title to render creation stats. User (with img), Date, Age.
	 * @return self
	 */
	public function titleCreation()
	{
	    /** @var $user GDO_User **/
	    $user = $this->gdo->gdoColumnOf(GDT_CreatedBy::class)->getValue();
	    $date = $this->gdo->gdoColumnOf(GDT_CreatedAt::class)->renderCell();
	    $age = $this->gdo->gdoColumnOf(GDT_CreatedAt::class)->renderAge();
	    if (module_enabled('Profile')) # ugly bridge
	    {
	        $profileLink = GDT_ProfileLink::make()->forUser($user)->withAvatar()->withNickname()->render();
	    }
	    else
	    {
	        $profileLink = $user->displayNameLabel();
	        $this->userAvatar($user);
	    }
	    $this->title(GDT_Label::make()->label('li_creation_title', [$profileLink, $date, $age]));
	    return $this;
	}
	
	
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
