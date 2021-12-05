<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\ModuleLoader;
use GDO\Core\Application;
use GDO\Core\Module_Core;
use GDO\Core\Website;

/**
 * This widget renders the ui/page.php template. the index.php of your site.
 * It holds all the bars and section hooks to render. calls them early and renders them later.
 * Another section is Website::topResponse()
 * 
 * @see UI/tpl/page.php - Override this template for your custom theme / menu / sidebars.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.3
 */
final class GDT_Page extends GDT
{
    public static $INSTANCE;
    
    use WithTitle;
    
    # The 4 nav areas-
    public $topNav;
    public $leftNav;
    public $rightNav;
    public $bottomNav;

    public $topTabs; # Admin or module tabs.
    
    public function defaultName() { return 'page'; }
    
    /**
     * Call nav hooks early
     */
    protected function __construct()
    {
        parent::__construct();
        self::$INSTANCE = $this;
        $this->reset();
    }
    
    public function reset()
    {
//         if (Module_Core::instance()->cfgLoadSidebars())
        {
            $this->topNav = GDT_Bar::make('topNav')->horizontal();
            $this->leftNav = GDT_Bar::make('leftNav')->vertical();
            $this->rightNav = GDT_Bar::make('rightNav')->vertical();
            $this->bottomNav = GDT_Bar::make('bottomNav')->horizontal();
        }
        $this->topTabs = GDT_Container::make('topTabs')->vertical();
        Website::$TOP_RESPONSE = null;
    }
    
    public function loadSidebars()
    {
        $app = Application::instance();
        
        if ($app->isInstall() || $app->isCLI())
        {
            return false;
        }
            
        foreach (ModuleLoader::instance()->getEnabledModules() as $module)
        {
            $module->onInitSidebar();
        }
        
        return true;
    }
    
    public function renderCell()
    {
    	if (module_enabled('Core'))
    	{
	        if (Module_Core::instance()->cfgLoadSidebars())
	        {
	            $this->loadSidebars();
	        }
    	}
        return GDT_Template::php('UI', 'page.php', ['page' => $this]);
    }
    
    public $html;
    public function html($html)
    {
        $this->html = $html;
        return $this;
    }

}
