<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\Application;

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
    use WithHTML;
    use WithTitle;
    
    # The 4 nav areas-
    public GDT_Bar $topNav;
    public GDT_Bar $leftNav;
    public GDT_Bar $rightNav;
    public GDT_Bar $bottomNav;
    
    public GDT_Bar $topTabs; # Admin or module tabs.
    
    /**
     * Call nav hooks early
     */
    public function __construct()
    {
        if (!Application::instance()->isInstall())
        {
            $this->topNav = GDT_Bar::make('topNav')->horizontal()->callHook('TopBar');
            $this->leftNav= GDT_Bar::make('leftNav')->vertical()->callHook('LeftBar');
            $this->rightNav = GDT_Bar::make('rightNav')->vertical()->callHook('RightBar');
            $this->bottomNav = GDT_Bar::make('bottomNav')->horizontal()->callHook('BottomBar');
            $this->topTabs = GDT_Bar::make('topTabs')->horizontal()->callHook('TopTabs');
        }
//         $this->topMessages = GDT_Container::make('topNav')->callHook('TopBar'$page);
    }
    
    public function renderCell()
    {
        return GDT_Template::php('UI', 'page.php', ['page' => $this]);
    }
}
