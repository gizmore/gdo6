<?php
namespace GDO\Language;
use GDO\Core\GDO_Module;
use GDO\UI\GDT_Bar;
use GDO\Util\Strings;
use GDO\Core\Application;
class Module_Language extends GDO_Module
{
    public $module_priority = 2;
    
    public function getClasses() { return ['GDO\Language\GDO_Language']; }
    public function onInstall() { LanguageData::onInstall(); }
    public function onLoadLanguage() { $this->loadLanguage('lang/language'); }

    public function getConfig()
    {
        return array(
            GDT_Language::make('languages')->all()->multiple()->initial('["'.GWF_LANGUAGE.'"]'),
        );
    }
    
    /**
     * Get the supported  languages, GWF_LANGUAGE first.
     * @return GDO_Language[]
     */
    public function cfgSupported()
    {
        $supported = [GWF_LANGUAGE => GDO_Language::table()->find(GWF_LANGUAGE)];
        if ($additional = $this->getConfigValue('languages'))
        {
            $supported = array_merge($supported, $additional);
        }
        return $supported;
    }
    
    public function onInit()
    {
        if (!Application::instance()->isCLI())
        {
            Trans::$ISO = $this->detectISO();
        }
    }
    
    public function detectISO()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            $languages = GDO_Language::table()->allSupported();
            if (preg_match_all("/[-a-zA-Z,]+;q=[.\d]+/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches))
            {
                foreach ($matches[0] as $match)
                {
                    list($isos, $q) = explode(';', ltrim($match, ','));
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
        return GWF_LANGUAGE;
    }
    
    public function hookRightBar(GDT_Bar $navbar)
    {
        $this->templatePHP('rightbar.php', ['navbar'=>$navbar]);
    }
}
