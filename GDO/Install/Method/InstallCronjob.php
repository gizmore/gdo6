<?php
namespace GDO\Install\Method;
use GDO\DB\Database;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT_Hook;
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
		$fields = [];
		GDT_Hook::callHook('InstallCronjob', $fields);
		return $this->templatePHP('page/installcronjob.php', ['fields' => $fields]);
	}
	
}
