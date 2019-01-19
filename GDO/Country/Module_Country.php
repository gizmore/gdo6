<?php
namespace GDO\Country;
use GDO\Core\GDO_Module;

/**
 * Country related functionality.
 * @author gizmore
 * @version 6.09
 */
class Module_Country extends GDO_Module
{
	public $module_priority = 3;
	
	public function getClasses() { return ['GDO\Country\GDO_Country']; }
	public function onInstall() { InstallCountries::install(); }
	public function onLoadLanguage() { $this->loadLanguage('lang/country'); }
}
