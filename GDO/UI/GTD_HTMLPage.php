<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * This widget renders the ui/page.php template. the index.php of your site.
 * It holds all the bars and section hooks to render. calls them early and renders them later. 
 * Another section is Website::topResponse
 * @author gizmore
 * @version 6.11
 * @since 6.11
 */
final class GDT_HTMLPage extends GDT
{
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
        $this->topNav = GDT_Bar::make('topNav')->callHook('TopBar', $this);
        $this->leftNav= GDT_Bar::make('leftNav')->callHook('LeftBar', $this);
        $this->rightNav = GDT_Bar::make('rightNav')->callHook('RightBar', $this);
        $this->bottomNav = GDT_Bar::make('bottomNav')->callHook('BottomBar', $this);
        $this->topTabs = GDT_Bar::make('topTabs')->callHook('TopTabs', $this);
//         $this->topMessages = GDT_Container::make('topNav')->callHook('TopBar', $this);
    }
    
    public function renderCell()
    {
        return GDT_Template::php('UI', 'ui/page.php', ['field' => $this]);
    }
}

