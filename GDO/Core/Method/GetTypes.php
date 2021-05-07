<?php
namespace GDO\Core\Method;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO;
use GDO\Core\Website;
/**
 * Get all types used in all tables.
 * Get the type class hierarchy.
 * @author gizmore
 */
final class GetTypes extends Method
{
	public function isAjax() { return true; }
	
	public function execute()
	{
		$tables = [];
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
								$tables[] = $table;
							}
						}
					}
				}
			}
		}
		
		# Sum table fields
		$fields = [];
		foreach ($tables as $table)
		{
			$fields[$table->gdoClassName()] = [];
			foreach ($table->gdoColumnsCache() as $name => $gdoType)
			{
			    if ($gdoType->isSerializable())
			    {
    				$fields[$table->gdoClassName()][$name] = array(
    					'type' => $gdoType->gdoClassName(),
    					'options' => $gdoType->configJSON(),
    				);
			    }
			}
		}
		
		# Build type hiararchy
		$types = [];
		foreach (get_declared_classes() as $class)
		{
			if (is_subclass_of($class, "GDO\\Core\\GDT"))
			{
				$types[$class] = array_values(class_parents($class));
			}
		}
		
		$json = ['fields' => $fields, 'types' => $types];
		Website::outputJSON($json);
	}
}
