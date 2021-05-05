<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\ModuleLoader;
use GDO\Core\Application;
use GDO\Core\Website;

/**
 * This widget renders the ui/page.php template. the index.php of your site.
 * It holds all the bars and section hooks to render. calls them early and renders them later.
 * Another section is Website::topResponse()
 * 
 * @author gizmore
 * @version 6.11
 * @since 6.11
 */
final class GDT_Page extends GDT
{
    public static $INSTANCE;
    
    use WithHTML;
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
        $this->topTabs = GDT_Container::make('topTabs')->vertical();
    }
    
    public function reset()
    {
        $this->topNav = null;
        $this->leftNav = null;
        $this->rightNav = null;
        $this->bottomNav = null;
        $this->topTabs = GDT_Container::make('topTabs')->vertical();
        Website::$TOP_RESPONSE = null;
    }
    
    public function loadSidebars()
    {
        $this->topNav = GDT_Bar::make('topNav')->horizontal();
        $this->leftNav= GDT_Bar::make('leftNav')->vertical();
        $this->rightNav = GDT_Bar::make('rightNav')->vertical();
        $this->bottomNav = GDT_Bar::make('bottomNav')->horizontal();
        $app = Application::instance();
        if (!$app->isInstall() && !$app->isCLI())
        {
            foreach (ModuleLoader::instance()->getEnabledModules() as $module)
            {
                if ($module->isPersisted())
                {
                    $module->onInitSidebar();
                }
            }
        }
    }
    
    public function renderCell()
    {
        return GDT_Template::php('UI', 'page.php', ['page' => $this]);
    }

}
