<?php
namespace GDO\GWF\Method;

use GDO\Core\Method;
use GDO\Core\GDO_Module;
use GDO\Core\ModuleLoader;
use GDO\Template\Response;
use GDO\Type\GDT_Secret;
use GDO\DB\DBType;
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
        $modules = ModuleLoader::instance()->getModules();
        foreach ($modules as $module)
        {
            $json[$module->getName()] = $this->getModuleConfig($module);
        }
        return new Response($json);
    }
    
    private function getModuleConfig(GDO_Module $module)
    {
        $json = [];
        foreach ($module->getConfigCache() as $type)
        {
            if ( ($type instanceof DBType) && 
                 (!$type instanceof GDT_Secret) )
            {
                $json[$type->name] = $module->getConfigValue($type->name);
            }
        }
        return $json;
    }
}
