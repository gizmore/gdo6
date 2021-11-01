<?php
namespace GDO\File;

use GDO\Core\GDO_Module;

/**
 * File related stuff is coverd by Module_File.
 * All files are stored in a single gdo_file table.
 * Other modules or GDO point to these files in that table.
 * 
 * @author gizmore@wechall.net
 * @version 6.10.4
 * @since 6.2.0
 */
final class Module_File extends GDO_Module
{
	public $module_priority = 10;

	public function isCoreModule() { return true; }
	
	public function getClasses()
	{
		return [
			GDO_File::class,
		];
	}
	
	public function onLoadLanguage() { return $this->loadLanguage('lang/file'); }
	
	public function getConfig()
	{
	    return [
	        GDT_Filesize::make('upload_max_size')->initial('16777216'),
	    ];
	}
	
	public function cfgUploadMaxSize() { return $this->getConfigValue('upload_max_size'); }
	
	public function onIncludeScripts()
	{
		$this->addBowerJS("flow.js/dist/flow.js");
		$this->addJS('js/gdo-flow.js');
	}
	
}
