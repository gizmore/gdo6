<?php
namespace GDO\Table;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_UInt;

/**
 * The table module allows some default settings for table responses.
 * Currently only ItemsPerPage is configurable.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.03
 * 
 * @see GDT_List
 */
final class Module_Table extends GDO_Module
{
	public function isCoreModule() { return true; }
	
	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
		    GDT_UInt::make('spr')->initial('20')->max(100),
		    GDT_UInt::make('ipp')->initial('20')->max(1000),
		);
	}
	public function cfgItemsPerPage() { return $this->getConfigValue('ipp'); }
	public function cfgSuggestionsPerRequest() { return $this->getConfigValue('spr'); }
	
}
