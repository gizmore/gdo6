<?php
namespace GDO\Date;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_User;
use GDO\UI\GDT_Page;
use GDO\Date\Method\Timezone;

/**
 * Date specific stuff.
 * - timezone javascript detection. default: on
 * - sidebar timezone select in left panel. default: on
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.10.1
 */
final class Module_Date extends GDO_Module
{
    public $module_priority = 8;
    public function isCoreModule() { return true; }
    public function onLoadLanguage() { return $this->loadLanguage('lang/date'); }
    
    ##############
    ### Config ###
    ##############
    public function getConfig()
    {
        return [
            GDT_Checkbox::make('tz_probe_js')->initial('1'),
            GDT_Checkbox::make('tz_sidebar_select')->initial('1'),
        ];
    }
    public function cfgProbeJS() { return $this->getConfigVar('tz_probe_js'); }
    public function cfgSidebarSelect() { return $this->getConfigVar('tz_sidebar_select'); }
 
    ############
    ### Init ###
    ############
    public function onIncludeScripts()
    {
        if ($this->cfgProbeJS())
        {
            if (!GDO_User::current()->hasTimezone())
            {
                $this->addJavascript('js/gdo6_timezone_probe.js');
            }
        }
    }
    
    public function onInitSidebar()
    {
        if ($this->cfgSidebarSelect())
        {
            if (!GDO_User::current()->hasTimezone())
            {
                GDT_Page::$INSTANCE->leftNav->addField(
                Timezone::make()->getForm());
            }
        }
    }

}
