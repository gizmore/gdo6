<?php
namespace GDO\Core\Method;

use GDO\Core\GDT_Array;
use GDO\Core\MethodAjax;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT;

final class UserSettings extends MethodAjax
{
    public function execute()
    {
        $modules = ModuleLoader::instance()->getEnabledModules();
        $settings = [];
        foreach ($modules as $module)
        {
            $moduleSettings = $module->getSettingsCache();
            $settings[$module->getName()] = [];
            foreach ($moduleSettings as $gdt)
            {
                if ($gdt->isSerializable())
                {
                    $settings[$module->getName()][] = $this->gdtSetting($gdt);
                }
            }
        }

        return GDT_Array::makeWith($settings);
    }

    private function gdtSetting(GDT $gdt)
    {
        return [
            'name' => $gdt->name,
            'var' => $gdt->var,
            'type' => get_class($gdt),
            'config' => $gdt->configJSON(),
        ];
    }

}
