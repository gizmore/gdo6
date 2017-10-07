<?php
namespace GDO\GWF\Method;

use GDO\Core\Method;
use GDO\DB\GDO;
use GDO\Core\ModuleLoader;
/**
 * Get all types used in all tables.
 * @author gizmore
 */
final class GetTypes extends Method
{
	public function isAjax() { return true; }
	
	public function execute()
	{
		# Add non abstract module tables
		foreach (ModuleLoader::instance()->getModules() as $module)
		{
			if ($classes = $module->getClasses())
			{
				foreach ($classes as $class)
				{
					if (is_subclass_of($class, 'GDO\\DB\\GDO'))
					{
						if ($table = GDO::tableFor($class))
						{
							if (!$table->gdoAbstract())
							{
								$tables[] = $table;
							}
						}
					}
				}
			}
		}
		
		# Add Enum values
		$types = [];
		foreach ($tables as $table)
		{
			$types[$table->gdoClassName()] = [];
			foreach ($table->gdoColumnsCache() as $name => $gdoType)
			{
				$types[$table->gdoClassName()][$gdoType->name] = $gdoType->gdoClassName();
			}
		}
		
		die(json_encode($types));
	}
}
