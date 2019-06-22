<?php
namespace GDO\Install;
use GDO\Core\GDO_Module;
use GDO\Core\Website;
/**
 * Module that features the installer.
 * The entry point is in gdo7/install/index.php
 * 
 * Provides the install theme to style the install wizard.
 * 
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class Module_Install extends GDO_Module
{
	public function onLoadLanguage() { $this->loadLanguage('lang/install'); }
	public function defaultEnabled() { return false; }
	public function getThemes() { return ['install']; }
	
	public function onInit()
	{
		$this->addCSS("css/install6.css");
	}
}
