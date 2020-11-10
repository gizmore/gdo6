<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\DB\GDT_CreatedBy;
use GDO\DB\GDT_CreatedAt;
use GDO\Avatar\GDT_Avatar;
use GDO\Profile\GDT_ProfileLink;
use GDO\Date\GDT_DateDisplay;

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
	use WithTitle;
	
	public $avatar;
	public function avatar($avatar) { $this->avatar = $avatar; return $this; }
	public function userAvatar(GDO_User $user) { return $this->avatar(GDT_Avatar::make()->user($user)); }
	
	public $subtitle;
	public function subtitle($subtitle) { $this->subtitle = $subtitle; return $this; }
	
	public $image;
	public function image($image) { $this->image = $image; return $this; }
	
	public $content;
	public function content($content) { $this->content = $content; return $this; }
	
	public $subtext;
	public function subtext($subtext) { $this->subtext = $subtext; return $this; }
	
	/**
	 * Use the subtitle to render creation stats. User (with avatar), Date, Age.
	 * @return self
	 */
	public function creatorHeader(GDT $title=null)
	{
	    /** @var $user GDO_User **/
	    $user = $this->gdo->gdoColumnOf(GDT_CreatedBy::class)->getValue();
	    $date = $this->gdo->gdoColumnOf(GDT_CreatedAt::class);
	    
	    $this->subtitle = GDT_Container::make();
	    
	    if (module_enabled('Avatar')) # ugly bridge
	    {
	        if (module_enabled('Profile'))
	        {
	            $this->avatar = GDT_ProfileLink::make()->forUser($user)->withAvatar();
	        }
	        else
	        {
	            $this->avatar = GDT_Avatar::make()->user($user);
	        }
	    }
	    
	    if (module_enabled('Profile')) # ugly bridge
	    {
	        $profileLink = GDT_ProfileLink::make()->forUser($user)->withNickname();
	    }
	    else
	    {
	        $profileLink = GDT_Label::make()->rawLabel($user->displayNameLabel());
	    }
	    $this->subtitle->addField($profileLink);
	    $this->subtitle->addField(GDT_DateDisplay::make($date->name)->gdo($this->gdo)->addClass('ri'));
	    
	    if ($title)
	    {
	        $this->title = $title;
	    }
        
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
			$this->actions = GDT_Menu::make()->label('actions');
		}
		return $this->actions;
	}
	
	public function renderCell() { return GDT_Template::php('UI', 'cell/list_item.php', ['gdt'=>$this]); }
	
}
