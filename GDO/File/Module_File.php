<?php
namespace GDO\File;

use GDO\Core\GDO_Module;

/**
 * File related stuff is coverd by Module_File.
 * 
 * @author gizmore@wechall.net
 * @version 6.10
 * @since 6.02
 */
final class Module_File extends GDO_Module
{
	public $module_priority = 10;

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
// 		$min = Module_Core::instance()->cfgMinifyJS() === 'no' ? '' : '.min';
		$this->addBowerJavascript("flow.js/dist/flow.js");
		$this->addJavascript('js/gdo-flow.js');
	}
}
