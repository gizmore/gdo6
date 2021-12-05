<?php
namespace GDO\Install;

use GDO\Core\GDO_Module;
use GDO\DB\Cache;

/**
 * Module that features the installer.
 * The entry point is in install/wizard.php
 * 
 * Provides the install theme to style the install wizard.
 * 
 * @see Installer
 * 
 * @author gizmore
 * @version 6.11.1
 * @since 6.0.0
 */
class Module_Install extends GDO_Module
{
	public function isInstallable() { return false; }

	public function onLoadLanguage() { $this->loadLanguage('lang/install'); }
	public function defaultEnabled() { return false; }
	
	public function getTheme()
	{
	    return 'install';
	}
	
	public function onInit()
	{
	    Cache::flush();
	    Cache::fileFlush();
	}
	
}
