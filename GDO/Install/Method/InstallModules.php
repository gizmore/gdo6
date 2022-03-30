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
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 3.0.0
 */
final class InstallModules extends Method
{
	/**
	 * @var GDO_Module[]
	 */
	private $modules;
	
	public function execute()
	{
	    Cache::fileFlush();
		Database::init();
		$loader = ModuleLoader::instance();
		$loader->loadModules(false, true, true);
		$this->modules = $loader->getInstallableModules();
		
		if (isset($_REQUEST['btn_install']))
		{
			return $this->onInstall(Common::getRequestArray('module'));
		}
		
		return $this->renderModuleTable();
	}
	
	public function renderModuleTable()
	{
		$tVars = array(
			'modules' => $this->modules,
			'moduleNames' => $this->getModuleNames(),
			'coreModules' => $this->getCoreModuleNames(),
			'siteModules' => $this->getSiteModuleNames(),
			'dependencies' => $this->getModuleDependencies(),
		);
		return $this->templatePHP('page/installmodules.php', $tVars);
	}
	
	private function getModuleNames()
	{
		$mods = [];
		foreach ($this->modules as $module)
		{
			$mods[] = $module->getName();
		}
		return $mods;
	}
	
	private function getCoreModuleNames()
	{
		$mods = [];
		foreach ($this->modules as $module)
		{
			if ($module->isCoreModule())
			{
				$mods[] = $module->getName();
			}
		}
		return $mods;
	}
	
	private function getSiteModuleNames()
	{
		$mods = [];
		foreach ($this->modules as $module)
		{
			if ($module->isSiteModule())
			{
				$mods[] = $module->getName();
			}
		}
		return $mods;
	}
	
	private function getModuleDependencies()
	{
		$deps = [];
		foreach ($this->modules as $module)
		{
			$deps[$module->getName()] = $module->dependencies();
		}
		return $deps;
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
			
			foreach ($this->modules as $module)
			{
				if ($module->isEnabled())
				{
					$module->onAfterInstall();
				}
			}
		}
		catch (\Exception $e)
		{
			Database::instance()->transactionRollback();
			throw $e;
		}
		finally
		{
			Cache::flush();
		}
		
		return $response->addField(GDT_Success::with(t('install_modules_completed', [Config::linkStep(5)])));
	}
	
}
