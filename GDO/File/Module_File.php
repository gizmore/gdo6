<?php
namespace GDO\File;
use GDO\Core\GDO_Module;
use GDO\Core\Module_Core;

/**
 * File related stuff is coverd by Module_File.
 * 
 * @author gizmore@wechall.net
 * @version 6.08
 *
 */
final class Module_File extends GDO_Module
{
	public $module_priority = 10;

	public function getClasses()
	{
		return array(
			'GDO\File\GDO_File', # File table.
		);
	}
	
	public function onLoadLanguage() { return $this->loadLanguage('lang/file'); }
	
	public function onIncludeScripts()
	{
		if (module_enabled('JQuery'))
		{
			$min = Module_Core::instance()->cfgMinifyJS() === 'no' ? '' : '.min';
			$this->addBowerJavascript("flow.js/lib/flow.js");
		}
	}
}
