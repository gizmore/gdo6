<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;

/**
 * A card with title, subtitle, creator, date, content and actions.
 *  
 * @author gizmore
 * @version 6.10
 * @since 6.04
 */
final class GDT_Card extends GDT
{
	use WithActions;
	use WithFields;
	use WithIcon;
	use WithTitle;
	use WithPHPJQuery;
	
	public function hasUpperCard()
	{
		return $this->title || $this->subtitle || $this->withCreator || $this->withCreated;
	}
	
	################
	### Subtitle ###
	################
	public $subtitle;
	public function subtitle($subtitle) { $this->subtitle = $subtitle; return $this; }
	
	##############
	### Render ###
	##############
	public function render() { return GDT_Template::php('UI', 'cell/card.php', ['field' => $this]); }
	public function renderCard() { return $this->render(); }
	
	###############
	### Creator ###
	###############
	public $withCreated;
	public function withCreated($bool=true) { $this->withCreated = $bool; return $this; }

	public $withCreator;
	public function withCreator($bool=true) { $this->withCreator = $bool; return $this; }
	
	public function gdoCreated()
	{
		return $this->gdo->gdoVarOf('GDO\DB\GDT_CreatedAt');
	}

	/**
	 * @return \GDO\User\GDO_User
	 */
	public function gdoCreator()
	{
		return $this->gdo->gdoValueOf('GDO\DB\GDT_CreatedBy');
	}
	
}
