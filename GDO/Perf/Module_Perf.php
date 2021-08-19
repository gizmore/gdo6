<?php
namespace GDO\Perf;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Enum;
use GDO\User\GDO_User;
use GDO\UI\GDT_Page;

/**
 * Performance statistics in footer.
 * Config perf_bottom_bar to restrict footer to staff or all or none.
 * This module is part of the gdo6 core.
 *
 * @author gizmore
 * @version 6.10.3
 * @since 5.3.0
 *
 * @see GDT_PerfBar
 */
final class Module_Perf extends GDO_Module
{
    public function onLoadLanguage() { return $this->loadLanguage('lang/perf'); }

    ##############
    ### Config ###
    ##############
    public function getConfig()
    {
        return [
            GDT_Enum::make('perf_bottom_bar')->enumValues('all', 'staff', 'none')->initial('staff'),
        ];
    }
    public function cfgBottomPermission() { return $this->getConfigVar('perf_bottom_bar'); }

    ############
    ### Hook ###
    ############
    /**
     * Show performance footer.
     */
    public function onInitSidebar()
	{
	    switch ($this->cfgBottomPermission())
	    {
	        case 'none': $show = false; break;
	        case 'staff': $show = GDO_User::current()->hasPermission('staff'); break;
	        case 'all': $show = true; break;
	    }
	    if ($show)
	    {
	        GDT_Page::$INSTANCE->bottomNav->addField(GDT_PerfBar::make('perf'));
	    }
	}

}
