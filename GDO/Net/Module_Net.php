<?php
namespace GDO\Net;

use GDO\Core\GDO_Module;

/**
 * Network related stuff.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.11.0
 */
final class Module_Net extends GDO_Module
{
	public $module_priority = 10;
	
	public function isCoreModule() { return true; }
	
	public function getClasses()
	{
		return [
			GDO_Domain::class,
			GDO_SubDomain::class,
		];
	}
	
}
