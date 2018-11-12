<?php
namespace GDO\Install\Method;
use GDO\DB\Database;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Util\Common;
use GDO\Core\GDO_Module;
use GDO\Install\Installer;
use GDO\Install\Config;
use GDO\Core\GDT_Success;
use GDO\DB\Cache;
use GDO\Core\GDT_Response;
/**
 * Install selected modules.
 * @author gizmore
 * @since 3.00
 * @version 6.05
 */
final class InstallModules extends Method
{
	/**
	 * @var GDO_Module[]
	 */
	private $modules;
	
	public function execute()
	{
		$db = Database::init();
		$loader = ModuleLoader::instance();
		$loader->loadModules(false, true);
		$loader->sortModules('module_priority');
		$this->modules = $loader->getModules();
		
		if (isset($_GET['btn_install']))
		{
			return $this->onInstall(Common::getGetArray('module'));
		}
		
		return $this->renderModuleTable();
	}
	
	public function renderModuleTable()
	{
		$tVars = array(
			'modules' => $this->modules,
		);
		return $this->templatePHP('page/installmodules.php', $tVars);
	}
	
	public function onInstall(array $toInstall)
	{
		$response = GDT_Response::make();
		try
		{
			foreach ($this->modules as $module)
			{
				$name = $module->getName();
				if (isset($toInstall[$name]))
				{
					Database::instance()->transactionBegin();
					$response->addHTML("Installing $name...");
					Installer::installModule($module);
					$response->addHTML("Done!<br/>\n");
					Database::instance()->transactionEnd();
				}
			}
		}
		catch (\Exception $e)
		{
			Database::instance()->transactionRollback();
//			 echo $response->renderHTML();
			throw $e;
		}
		finally
		{
			Cache::flush();
		}
		
		return $response->addField(GDT_Success::with(t('install_modules_completed', [Config::linkStep(5)])));
	}
}
