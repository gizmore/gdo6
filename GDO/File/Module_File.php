<?php
namespace GDO\File;

use GDO\Core\GDO_Module;

/**
 * File related stuff is covered by Module_File.
 * All files are stored in a single gdo_file table.
 * Other modules or GDO point to these files in that table.
 * Uploading is chunky done via flow.js, if possible. A $_FILES fallback is in use.
 * 
 * @see GDT_File
 * @see GDT_Files
 * @see GDO_File
 * @see GDO_FileTable
 * @see GDT_ImageFiles
 * 
 * @TODO Make Module_File an optional module.
 * 
 * @author gizmore@wechall.net
 * @version 6.11.0
 * @since 6.2.0
 */
final class Module_File extends GDO_Module
{
	public $module_priority = 10;

	##############
	### Module ###
	##############
	public function isCoreModule() { return true; }
	
	public function getDependencies()
	{
		# @TODO remove Cronjob dependency by scaling images on the fly. Add cronjob dependencies where necessary.
		return ['Cronjob'];
	}
	
	public function getClasses()
	{
		return [
			GDO_File::class,
		];
	}
	
	public function onLoadLanguage() { return $this->loadLanguage('lang/file'); }
	
	public function onIncludeScripts()
	{
		$this->addBowerJS("flow.js/dist/flow.js");
		$this->addJS('js/gdo-flow.js');
	}
	
	##############
	### Config ###
	##############
	public function getConfig()
	{
		return [
			GDT_Filesize::make('upload_max_size')->initial('16777216'),
		];
	}
	public function cfgUploadMaxSize() { return $this->getConfigValue('upload_max_size'); }

}
