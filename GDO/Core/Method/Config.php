<?php
namespace GDO\Core\Method;

use GDO\Core\GDO_Module;
use GDO\Core\ModuleLoader;
use GDO\Core\Website;
use GDO\Core\MethodAjax;
/**
 * API Request to get all module configs.
 * Useful for JS Apps.
 * @author gizmore
 */
final class Config extends MethodAjax
{
	public function execute()
	{
		$json = [];
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$json[$module->getName()] = $this->getModuleConfig($module);
		}
		Website::renderJSON($json);
	}
	
	private function getModuleConfig(GDO_Module $module)
	{
		$json = [];
		foreach ($module->getConfigCache() as $type)
		{
			$value = $module->getConfigValue($type->name);
// 			if ($value instanceof GDO)
// 			{
// 				$value = $value->getID();
// 			}
			$json[$type->name] = $type->toVar($value);
		}
		return $json;
	}
}
