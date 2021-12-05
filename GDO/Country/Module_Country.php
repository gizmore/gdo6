<?php
namespace GDO\Country;

use GDO\Core\GDO_Module;

/**
 * Country related functionality.
 * @TODO: Make own, non core, Module_Country. userSettings: country. 
 * @TODO: Country/img flages should be named uppercase. cannot do this nicely on window.  
 * @author gizmore
 * @version 6.10
 * @since 3.00
 */
class Module_Country extends GDO_Module
{
	public $module_priority = 2;
	
	public function isCoreModule() { return true; }
	
	public function thirdPartyFolders() { return ['/img/']; }
	
	public function getClasses() { return [GDO_Country::class]; }
	public function onInstall() { InstallCountries::install(); }
	public function onLoadLanguage() { $this->loadLanguage('lang/country'); }

}
