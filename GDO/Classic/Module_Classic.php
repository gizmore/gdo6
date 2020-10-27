<?php
namespace GDO\Classic;

use GDO\Core\GDO_Module;
use GDO\Core\Application;

/**
 * A module that adds sidebar behaviour to the default theme.
 * 
 * @author gizmore
 *
 */
final class Module_Classic extends GDO_Module
{
	public $module_priority = 10; # Include css rather early
	
	public function onIncludeScripts()
	{
	    if (in_array('classic', Application::instance()->getThemes(), true))
	    {
    		$this->addCSS('css/gdo6.css');
    		$this->addCSS('css/gdo6-sidebar.css');
    		$this->addCSS('css/gdo6-classic.css');
    		$this->addCSS('css/gdo6-pulse.css');
	    }
	}
}
