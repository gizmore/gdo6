<?php
namespace GDO\Core\Method;
use GDO\Core\Method;
use GDO\Core\GDO_Module;
use GDO\Core\ModuleLoader;
use GDO\Core\Website;
/**
 * API Request to get all module configs.
 * Useful for JS Apps.
 * @author gizmore
 */
final class Config extends Method
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
			$json[$type->name] = $module->getConfigValue($type->name);
		}
		return $json;
	}
}
