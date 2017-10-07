<?php
namespace GDO\Core\Method;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO;
use GDO\DB\GDT_Enum;
/**
 * Get enum values for all tables
 * @author gizmore
 */
final class GetEnums extends Method
{
	public function isAjax() { return true; }
	
	public function execute()
	{
		$tables = [];
		
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
		$columns = [];
		foreach ($tables as $table)
		{
			foreach ($table->gdoColumnsCache() as $name => $gdoType)
			{
				if ($gdoType instanceof GDT_Enum)
				{
					$columns[$table->gdoClassName().'.'.$name] = $gdoType->enumValues;
				}
			}
		}
		
		die(json_encode($columns));
	}
}
