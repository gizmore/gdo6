<?php
namespace GDO\Cronjob;

use GDO\Core\GDO_Module;

class Module_Cronjob extends GDO_Module
{
	##############
	### Module ###
	##############
	public function onLoadLanguage() { return $this->loadLanguage('lang/cronjob'); }

	public function href_administrate_module()
	{
		return href('Cronjob', 'Cronjob');
	}

	
}
