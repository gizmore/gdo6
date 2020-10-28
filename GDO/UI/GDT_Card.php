<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\DB\GDT_CreatedBy;
use GDO\DB\GDT_CreatedAt;
use GDO\Profile\GDT_ProfileLink;
use GDO\Avatar\GDT_Avatar;
use GDO\Date\GDT_DateDisplay;
use GDO\Core\WithFields;
use GDO\DB\GDT_EditedBy;
use GDO\DB\GDT_EditedAt;

/**
 * A card with title, subtitle, creator, date, content and actions.
 *  
 * @author gizmore
 * @version 6.10
 * @since 6.04
 */
final class GDT_Card extends GDT
{
	use WithTitle;
	use WithFields;
	use WithActions;
	use WithPHPJQuery;
	
	################
	### Subtitle ###
	################
	/** @var $subtitle GDT **/
	public $subtitle;
	public function subtitle($subtitle) { $this->subtitle = $subtitle; return $this; }
	
	##############
	### Avatar ###
	##############
	/** @var $avatar GDT **/
	public $avatar;
	public function avatar($avatar) { $this->avatar = $avatar; return $this; }
	
	###############
	### Content ###
	###############
	/** @var $content GDT **/
	public $content;
	public function content($content) { $this->content = $content; return $this; }
	
	#############
	### Image ###
	#############
	/** @var $image GDT **/
	public $image;
	public function image($image) { $this->image = $image; return $this; }
	
	##############
	### Footer ###
	##############
	/** @var $footer GDT **/
	public $footer;
	public function footer($footer) { $this->footer = $footer; return $this; }
	
	##############
	### Render ###
	##############
	public function render() { return GDT_Template::php('UI', 'cell/card.php', ['field' => $this]); }
	public function renderCard() { return $this->render(); }
	
	######################
	### Creation title ###
	######################
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
	
	#####################
	### Edited Footer ###
	#####################
	/**
	 * Create a last 'edited by' footer.
	 */
	public function editorFooter()
	{
	    /** @var $user GDO_User **/
	    if ($user = $this->gdo->gdoColumnOf(GDT_EditedBy::class)->getValue())
	    {
    	    if (module_enabled('Profile'))
    	    {
    	        $username = GDT_ProfileLink::make()->forUser($user)->withNickname()->withAvatar()->renderCell();
    	    }
    	    else
    	    {
    	        $username = $user->displayNameLabel();
    	    }
    	    
    	    $at = $this->gdo->gdoColumnOf(GDT_EditedAt::class)->renderCell();
    	    $this->footer = GDT_Label::make()->label('edited_info', [$username, $at]);
	    }
	    return $this;
	}
}
