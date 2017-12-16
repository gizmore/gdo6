<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
/**
 * 
 * @author gizmore
 */
final class GDT_Card extends GDT
{
    use WithActions;
    use WithFields;
    use WithIcon;
    use WithTitle;
    
    ##############
    ### Render ###
    ##############
    public function render() { return GDT_Template::php('UI', 'cell/card.php', ['field' => $this]); }
    
    ###############
    ### Creator ###
    ###############
    public function gdoCreated()
    {
    	if ($gdoType = $this->gdo->gdoColumnOf('GDO\DB\GDT_CreatedAt'))
    	{
    		return $gdoType->getVar();
    	}
    }

    /**
     * @return \GDO\User\GDO_User
     */
    public function gdoCreator()
    {
    	if ($gdoType = $this->gdo->gdoColumnOf('GDO\DB\GDT_CreatedBy'))
    	{
    		return $gdoType->getValue();
    	}
    }
}
