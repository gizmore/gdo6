<?php
namespace GDO\Core\Method;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO;
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
				$fields[$table->gdoClassName()][$gdoType->name] = array(
					'type' => $gdoType->gdoClassName(),
					'options' => $gdoType->configJSON(),
				);
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
		
		die(json_encode(['fields' => $fields, 'types' => $types]));
	}
}
