<?php
namespace GDO\Table;
use GDO\Core\GDO_Module;
use GDO\DB\GDT_UInt;
/**
 * The table module allows some default settings for table responses.
 * Currently only ItemsPerPage is configurable.
 * @author gizmore
 * @version 6.05
 */
final class Module_Table extends GDO_Module
{
	public function getConfig()
	{
		return array(
			GDT_UInt::make('ipp')->initial('10')->max(1000),
		);
	}
	public function cfgItemsPerPage() { return $this->getConfigValue('ipp'); }
}
