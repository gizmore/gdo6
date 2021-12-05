<?php
namespace GDO\Install\Method;
use GDO\DB\Database;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT_Hook;
use GDO\UI\GDT_Container;
/**
 * Show info howto install cronjob
 * @author gizmore
 */
final class InstallCronjob extends Method
{
	public function execute()
	{
		Database::init();
		$hasdb = GDO_DB_HOST !== null;
		ModuleLoader::instance()->loadModules($hasdb, !$hasdb);
		return $this->renderPage();
	}
	
	public function renderPage()
	{
		$container = GDT_Container::make()->vertical(true);
		GDT_Hook::callHook('InstallCronjob', $container);
		return $this->templatePHP('page/installcronjob.php', ['container' => $container]);
	}
	
}
