<?php
namespace GDO\Cronjob;

use GDO\Core\GDO_Module;

/**
 * Cronjob stuff.
 * 
 * @TODO add a helper for run frequencies for cronjob methods.
 * @author gizmore
 * @version 6.10.4
 * @since 6.1.0
 */
class Module_Cronjob extends GDO_Module
{
	##############
	### Module ###
	##############
    public function isCoreModule() { return true; }
    
    public function getClasses()
    {
        return [
            GDO_Cronjob::class,
        ];
    }
    
	public function onLoadLanguage()
	{
	    return $this->loadLanguage('lang/cronjob');
	}

	public function href_administrate_module()
	{
		return href('Cronjob', 'Cronjob');
	}
	
}
