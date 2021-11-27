<?php
namespace GDO\Core\Method;

use GDO\Core\GDT_Array;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO;
use GDO\DB\GDT_Enum;

/**
 * Get enum values for all entities and GDT.
 * @author gizmore
 */
final class GetEnums extends Method
{
	public function isAjax() { return true; }
	
	public function execute()
	{
		$columns = [];
		
		# Add non abstract module tables
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			if ($classes = $module->getClasses())
			{
				foreach ($classes as $class)
				{
					if (is_subclass_of($class, 'GDO\\Core\\GDO'))
					{
						if ($table = GDO::tableFor($class))
						{
							if (!$table->gdoAbstract())
							{
								foreach ($table->gdoColumnsCache() as $name => $gdt)
								{
									if ($gdt instanceof GDT_Enum)
									{
										$columns[$table->gdoClassName().'.'.$name] = $gdt->enumValues;
									}
								}
							}
						}
					}
				}
			}
			
			if ($config = $module->getConfigCache())
			{
				foreach ($config as $gdt)
				{
					if ($gdt instanceof GDT_Enum)
					{
						$columns[$module->getName().'.config.'.$gdt->name] = $gdt->enumValues;
					}
				}
			}
		
			if ($config = $module->getUserConfig())
			{
				foreach ($config as $gdt)
				{
					if ($gdt instanceof GDT_Enum)
					{
						$columns[$module->getName().'.userconfig.'.$name] = $gdt->enumValues;
					}
				}
			}

			if ($config = $module->getUserSettings())
			{
				foreach ($config as $gdt)
				{
					if ($gdt instanceof GDT_Enum)
					{
						$columns[$module->getName().'.settings.'.$name] = $gdt->enumValues;
					}
				}
			}
		}
		
		return GDT_Array::makeWith($columns);
	}
}
