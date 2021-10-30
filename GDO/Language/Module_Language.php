<?php
namespace GDO\Language;

use GDO\Core\GDO_Module;
use GDO\Util\Strings;
use GDO\Core\Application;
use GDO\User\GDO_User;
use GDO\Session\GDO_Session;
use GDO\Javascript\Javascript;
use GDO\Util\Common;
use GDO\Core\Website;
use GDO\UI\GDT_Divider;
use GDO\DB\GDT_Checkbox;
use GDO\UI\GDT_Page;

/**
 * Internationalization Module.
 *
 * - Detect language by cookie or http_accept_language
 * - Provide lang switcher via cookie
 * - Provide language select
 * - Provide GDO_Language table
 *
 * @author gizmore
 * @version 6.10.4
 * @since 2.0.0
 */
final class Module_Language extends GDO_Module
{
    public $module_priority = 2;
    
    public function isCoreModule() { return true; }
    public function getClasses() { return [GDO_Language::class]; }
    public function onInstall() { LanguageData::onInstall(); }
    public function onLoadLanguage() { return $this->loadLanguage('lang/language'); }
    
    ##############
    ### Config ###
    ##############
    public function getConfig()
    {
        return [
            GDT_Language::make('languages')->all()->multiple()->initial('["'.GDO_LANGUAGE.'"]'),
            GDT_Checkbox::make('langswitch_left')->initial('1'),
            GDT_Checkbox::make('use_in_javascript')->initial('1'),
        ];
    }
    
    public function cfgSwitchLeft() { return $this->getConfigValue('langswitch_left'); }
    public function cfgJavascript() { return $this->getConfigValue('use_in_javascript'); }
    
    /**
     * Get the supported  languages, GDO_LANGUAGE first.
     * @return GDO_Language[]
     */
    public function cfgSupported()
    {
        $supported = [GDO_LANGUAGE => GDO_Language::table()->find(GDO_LANGUAGE)];
        if ($additional = $this->getConfigValue('languages'))
        {
            $supported = array_merge($supported, $additional);
        }
        return $supported;
    }
    
    ############
    ### Init ###
    ############
    public function onInit()
    {
        $iso = $this->detectISO();
        Trans::setISO($iso);
        if (!Application::instance()->isCLI())
        {
            Website::addMeta(['language', $iso, 'name']);
        }
    }
    
    public function onInitSidebar()
    {
        if ($this->cfgSwitchLeft())
        {
            $navbar = GDT_Page::$INSTANCE->leftNav;
            $navbar->addField(GDT_LangSwitch::make());
            $navbar->addField(GDT_Divider::make());
        }
    }
    
    public function onIncludeScripts()
    {
        # If enabled include js trans data and translation engine.
        if ($this->cfgJavascript())
        {
            # Add js trans
            $iso = Trans::$ISO;
            $href = $this->href('GetTransData', "&_ajax=1&_fmt=json&iso={$iso}&".$this->nocacheVersion());
            Javascript::addJavascript($href);
            
            # Add cheap translation engine.
            $this->addJavascript('js/gdo-trans.js');
        }
    }
    
    #################
    ### Detection ###
    #################
    public function detectISO()
    {
        if ($iso = Common::getRequestString('_lang'))
        {
            return $iso;
        }
        if ($iso = GDO_Session::get('gdo-language'))
        {
            return $iso;
        }
        if (!($iso = $this->detectAcceptLanguage()))
        {
            $iso = GDO_User::current()->getLangISO();
        }
        return $iso ? $iso : GDO_LANGUAGE;
    }
    
    public function detectAcceptLanguage()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            $matches = [];
            $languages = GDO_Language::table()->allSupported();
            if (preg_match_all("/[-a-zA-Z,]+;q=[.\d]+/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches))
            {
                foreach ($matches[0] as $match)
                {
                    list($isos) = explode(';', ltrim($match, ','));
                    foreach (explode(',', $isos) as $iso)
                    {
                        $iso = strtolower(Strings::substrTo($iso, '-', $iso));
                        if (isset($languages[$iso]))
                        {
                            return $iso;
                        }
                    }
                }
            }
        }
    }

}
