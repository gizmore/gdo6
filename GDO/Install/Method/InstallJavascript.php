<?php
namespace GDO\Install\Method;
use GDO\DB\Database;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
/**
 * Show info howto install bower components.
 * @author gizmore
 */
final class InstallJavascript extends Method
{
	public function execute()
	{
		Database::init();
		ModuleLoader::instance()->loadModulesA();
		return $this->renderPage();
	}

	public function renderPage()
	{
		return $this->templatePHP('page/installjavascript.php');
	}
}
