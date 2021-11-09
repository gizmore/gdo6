<?php
namespace GDO\Install\Method;
use GDO\DB\Database;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
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
		return $this->templatePHP('page/installcronjob.php');
	}
}
