<?php
namespace GDO\Install\Method;
use GDO\DB\Database;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Util\Common;
use GDO\Core\GDO_Module;
use GDO\Install\Installer;
use GDO\Install\Config;
use GDO\UI\GDT_Panel;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Success;
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
        Database::init();
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
        try
        {
            foreach ($this->modules as $module)
            {
                Database::instance()->transactionBegin();
                $name = $module->getName();
                if (isset($toInstall[$name]))
                {
                    Installer::installModule($module);
                }
                Database::instance()->transactionEnd();
            }
        }
        catch (\Exception $e)
        {
            Database::instance()->transactionRollback();
            throw $e;
        }
        
        return GDT_Success::with(t('install_modules_completed', [Config::linkStep(5)]));
    }
}
