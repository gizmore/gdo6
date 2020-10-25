<?php
namespace GDO\Perf;

use GDO\UI\GDT_Bar;
use GDO\Core\GDO_Module;
use GDO\DB\GDT_Enum;
use GDO\User\GDO_User;

/**
 * Performance statistics in footer.
 * Config perf_bottom_bar to restrict footer to staff or all or none.
 * This module is part of the gdo6 core.
 * 
 * @author gizmore
 * @version 6.10
 * @since 5.03
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
        return array(
            GDT_Enum::make('perf_bottom_bar')->enumValues('all', 'staff', 'none')->initial('staff'),
        );
    }
    public function cfgBottomPermission() { return $this->getConfigVar('perf_bottom_bar'); }

    ############
    ### Hook ###
    ############
    /**
     * Show performance footer.
     * @param GDT_Bar $bottomBar
     */
	public function hookBottomBar(GDT_Bar $bottomBar)
	{
	    switch ($this->cfgBottomPermission())
	    {
	        case 'none': $show = false; break;
	        case 'staff': $show = GDO_User::current()->hasPermission('staff'); break;
	        case 'all': $show = true;
	    }
	    if ($show)
	    {
	        $bottomBar->addField(GDT_PerfBar::make('perf'));
	    }
	}

}
