<?php
namespace GDO\Table;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_UInt;
use GDO\Core\Application;

/**
 * The table module allows some default settings for table responses.
 * Currently only ItemsPerPage is configurable.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.3
 * 
 * @see GDT_List
 * @see GDT_Table
 * @see MethodTable
 * @see MethodQueryTable
 */
final class Module_Table extends GDO_Module
{
	public $module_priority = 10;

	public function isCoreModule() { return true; }

	##############
	### Config ###
	##############
	public function getConfig()
	{
		return [
		    GDT_UInt::make('spr')->initial('20')->min(1)->max(100),
		    GDT_UInt::make('ipp_cli')->initial('10')->min(1)->max(1000),
		    GDT_UInt::make('ipp_http')->initial('20')->min(1)->max(1000),
		];
	}
	public function cfgSuggestionsPerRequest() { return $this->getConfigValue('spr'); }
	public function cfgItemsPerPageCLI() { return $this->getConfigValue('ipp_cli'); }
	public function cfgItemsPerPageHTTP() { return $this->getConfigValue('ipp_http'); }
	public function cfgItemsPerPage()
	{
	    return Application::instance()->isCLI() ?
	       $this->cfgItemsPerPageCLI() :
	       $this->cfgItemsPerPageHTTP();
	}

}
