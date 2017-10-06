<?php
namespace GDO\Core;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\DB\GDT_AutoInc;
use GDO\Language\Trans;
use GDO\DB\GDT_Int;
use GDO\DB\GDT_Name;
use GDO\Table\GDT_Sorting;
use GDO\DB\GDT_Version;
use GDO\User\GDO_UserSetting;
use GDO\Util\Javascript;
use GDO\Util\Strings;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_UserSettingBlob;
/**
 * GDO base module class.
 * @author gizmore
 * @since 1.00
 * @version 7.00
 */
class GDO_Module extends GDO
{
    ################
    ### Override ###
    ################
    public $module_version = "7.00";
    public $module_author = "Christian Busch <gizmore@wechall.net>";
    public $module_license = "GDO7";
    public $module_priority = 50;
    
    public function gdoCached() { return false; }
    public function memCached() { return false; }
    public function defaultEnabled() { return true; }
    
    /**
     * Provided theme names in module /thm/$themeName folder.
     * @return string[] array of $themeNames
     */
    public function getThemes() {}
    
    /**
     * GDO classes to install.
     * @return string[]
     */
    public function getClasses() {}
    
    /**
     * Module config GDTs
     * @return GDT[]
     */
    public function getConfig() { return []; }
    
    ##############
    ### Config ###
    ##############
    /**
     * @var GDT[]
     */
    private $configCache;
    public function getConfigCache()
    {
        if (!$this->configCache)
        {
            $this->configCache = $this->getConfig();
            foreach ($this->configCache as $gdoType)
            {
                $gdoType->val($this->getConfigVar($gdoType->name));
            }
        }
        return $this->configCache;
    }
    
    public function getConfigColumn($key)
    {
        foreach ($this->getConfigCache() as $gdoType)
        {
            if ($gdoType->name === $key)
            {
                return $gdoType;
            }
        }
    }
    
    public function getConfigVar($key)
    {
        return $this->getConfigColumn($key)->getVar();
    }
    
    public function getConfigValue($key)
    {
        return $this->getConfigColumn($key)->getValue();
    }
    
    public function saveConfigVar($key, $var)
    {
        GDO_ModuleVar::createModuleVar($this, $this->getConfigColumn($key)->val($var));
        Cache::remove('gdo_modules');
    }
    
    public function saveConfigValue($key, $value)
    {
        GDO_ModuleVar::createModuleVar($this, $this->getConfigColumn($key)->value($value));
        Cache::remove('gdo_modules');
    }
    
    ##############
    ### Events ###
    ##############
    public function onInit() {}
    public function onInstall() {}
    public function onWipe() {}
    public function onLoad() {}
    public function onLoadLanguage() {}
    public function onIncludeScripts() {}
    
    ###########
    ### GDO ###
    ###########
    public function gdoColumnsCache() { return Database::columnsS('GDO\Core\GDO_Module'); } # Polymorph fix
    public function gdoTableName() { return "gdo_module"; } # Polymorph fix
    public function gdoClassName() { return 'GDO\Core\GDO_Module'; } # Polymorph fix
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('module_id'),
            GDT_Name::make('module_name')->notNull()->unique(),
            GDT_Int::make('module_priority')->notNull()->unsigned()->initial($this->module_priority),
            GDT_Sorting::make('module_sort'),
            GDT_Version::make('module_version')->notNull()->initial('0.00'),
            GDT_Checkbox::make('module_enabled')->notNull()->initial('0'),
        );
    }
    
    ##############
    ### Static ###
    ##############
    /**
     * @return self
     */
    public static function instance() { return ModuleLoader::instance()->getModule(self::getNameS()); }
    public static function getNameS() { return Strings::substrFrom(get_called_class(), 'Module_'); }
    
    ##############
    ### Getter ###
    ##############
    public function getID() { return $this->getVar('module_id'); }
    public function getName() { return $this->getVar('module_name'); }
    public function getVersion() { return $this->getVar('module_version'); }
    public function isEnabled() { return !!$this->getVar('module_enabled'); }
    public function isCoreModule() { return false; }
    public function getSiteName() { return sitename(); }
    
    ###############
    ### Display ###
    ###############
    public function render_fs_version() { return $this->module_version; }
    
    ############
    ### Href ###
    ############
    public function href_install_module() { return href('Admin', 'Install', '&module='.$this->getName()); }
    public function href_configure_module() { return href('Admin', 'Configure', '&module='.$this->getName()); }
    public function href_administrate_module() {}
    
    ##############
    ### Helper ###
    ##############
    public function canUpdate() { return $this->module_version != $this->getVersion(); }
    public function canInstall() { return !$this->isPersisted(); }
    public function filePath($path='') { return GWF_PATH.$this->wwwPath($path); }
    public function wwwPath($path='') { return "GDO/{$this->getName()}/$path"; }
    public function includeClass($class) { require_once $this->filePath("$class.php"); }
    
    #################
    ### Templates ###
    #################
    /**
     * @param string $file
     * @param array $tVars
     * @return GDT_Response
     */
    public function templatePHP($file, array $tVars=null)
    {
        switch (Application::instance()->getFormat())
        {
            case 'json': return new GDT_Response($tVars);
            case 'html': default: return new GDT_Response(GDT_Template::php($this->getName(), $file, $tVars));
        }
    }
    public function templateFile($file) { return GDT_Template::file($this->getName(), $file); }
    public function error($key, array $args=null) { return GDT_Error::responseWith($key, $args); }
    public function message($key, array $args=null) { return GDT_Success::responseWith($key, $args); }
    
    ############
    ### Init ###
    ############
    public function __wakeup() { $this->inited = false; } # TODO: wakeup knows that language and settings are also memcached soon? :)
    private $inited = false;
    public function initModule()
    {
        if (!$this->inited)
        {
            $this->inited = true;
            $this->onLoadLanguage();
            $this->registerSettings();
            if ($this->isPersisted() && $this->isEnabled())
            {
                $app = Application::instance();
                if ( (!$app->isInstall()) && (!$app->isCLI()) )
                {
                    $this->onIncludeScripts();
                }
                $this->onInit();
            }
        }
    }
    
    public function registerThemes()
    {
        if ($themes = $this->getThemes())
        {
            foreach ($themes as $theme)
            {
                GDT_Template::registerTheme($theme, $this->filePath("thm/$theme/"));
            }
        }
    }
    
    public function loadClasses()
    {
        if ($classes = $this->getClasses())
        {
            foreach ($classes as $class)
            {
                $this->includeClass($class);
            }
        }
    }
    
    public function loadLanguage($path)
    {
        Trans::addPath($this->filePath($path));
        return $this;
    }
    
    ###################
    ### User config ###
    ###################
    /**
     * @return GDT[]
     */
    public function getUserConfig(){}
    /**
     * @return GDT[]
     */
    public function getUserSettings(){}
    public function getUserSettingBlobs(){}
    public function registerSettings()
    {
        if ($settings = $this->getUserConfig())
        {
            foreach ($settings as $setting)
            {
                GDO_UserSetting::register($setting);
            }
        }
        if ($settings = $this->getUserSettings())
        {
            foreach ($settings as $setting)
            {
                GDO_UserSetting::register($setting);
            }
        }
        if ($settings = $this->getUserSettingBlobs())
        {
            foreach ($settings as $setting)
            {
                GDO_UserSettingBlob::register($setting);
            }
        }
    }
    
    ##############
    ### Method ###
    ##############
    /**
     * @param string $methodName
     * @return Method
     */
    public function getMethod($methodName)
    {
        $klass = "GDO\\{$this->getName()}\\Method\\{$methodName}";
        return new $klass;
    }
    
//     public function getMethodHREF($methodName, $append='')
//     {
//         return href($this->getName(), $methodName, $append);
//     }
    
    ##############
    ### Assets ###
    ##############
    public function addBowerJavascript($path) { return $this->addJavascript('bower_components/'.$path); }
    public function addJavascript($path) { return Javascript::addJavascript($this->wwwPath($path)); }
    public function addBowerCSS($path) { return $this->addCSS('bower_components/'.$path); }
    public function addCSS($path) { return Website::addCSS($this->wwwPath($path)); }

}
