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
use GDO\Core\Application;
use GDO\Table\GDT_List;

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
	
	public $avatar;
	public function avatar($avatar) { $this->avatar = $avatar; return $this; }
	public function userAvatar(GDO_User $user) { return $this->avatar(GDT_Avatar::make()->user($user)); }
	
	public $title;
	public function title($title) { $this->title = $title; return $this; }
	
	public $subtitle;
	public function subtitle($subtitle) { $this->subtitle = $subtitle; return $this; }
	
	public $image;
	public function image($image) { $this->image = $image; return $this; }
	
	public $content;
	public function content($content) { $this->content = $content; return $this; }
	
	public $right;
	public function right($content) { $this->right = $content; return $this; }
	
	public $subtext;
	public function subtext($subtext) { $this->subtext = $subtext; return $this; }
	
	/**
	 * Use the subtitle to render creation stats. User (with avatar), Date, Age.
	 * @return self
	 */
	public function creatorHeader(GDT $title=null, $dateformat='short')
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
	        $profileLink = GDT_Label::make()->labelRaw($user->displayNameLabel());
	    }
	    $this->subtitle->addField($profileLink);
	    $this->subtitle->addField(GDT_DateDisplay::make($date->name)->dateformat($dateformat)->gdo($this->gdo)->addClass('ri'));
	    
	    if ($title)
	    {
	        $this->title = $title;
	    }
        
	    return $this;
	}
	
// 	public function editorFooter()
// 	{
// 	    $this->goo
// 	    return t('foozzzz');
// 	}
	
	/**
	 * @var GDT_Menu
	 */
	public $actions;
	public function actions()
	{
		if (!$this->actions)
		{
		    $this->actions = GDT_Menu::make('actions')->label('actions');
		}
		return $this->actions;
	}
	
	public function renderCell() { return GDT_Template::php('UI', 'cell/list_item.php', ['gdt'=>$this]); }
	
	public function render()
	{
	    switch (Application::instance()->getFormat())
	    {
	        case 'json': 
	            GDT_List::$CURRENT->data[] = $this->renderJSON();
	            break;
	        case 'xml':
                GDT_List::$CURRENT->data[] = $this->renderJSON();
                break;
	        default:
	            return $this->renderCell();
	    }
	}
	
	public function renderJSON()
	{
	    $data = [];
	    if ($this->title)
	    {
	        $data['title'] = $this->title->renderCell();
	    }
	    if ($this->subtitle)
	    {
	        $data['subtitle'] = $this->subtitle->renderCell();
	    }
	    if ($this->content)
	    {
	        $data['content'] = $this->content->renderCell();
	    }
	    if ($this->right)
	    {
	        $data['right'] = $this->right->renderCell();
	    }
	    if ($this->subtext)
	    {
	        $data['subtext'] = $this->subtext->renderCell();
	    }
	    return $data;
	}
	
}
