<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\DB\GDT_AutoInc;
use GDO\Language\Trans;
use GDO\DB\GDT_Int;
use GDO\DB\GDT_Name;
use GDO\DB\GDT_Version;
use GDO\User\GDO_UserSetting;
use GDO\Javascript\Javascript;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_UserSettingBlob;
use GDO\User\GDO_User;
use GDO\DB\GDT_Text;
use GDO\Tests\Module_Tests;
use GDO\Table\GDT_Sort;
use GDO\File\FileUtil;
use GDO\Util\Strings;
use GDO\UI\GDT_Link;

/**
 * GDO base module class.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 1.0.0
 */
class GDO_Module extends GDO
{
	################
	### Override ###
	################
	public $module_version = "6.11";
	public $module_author = "Christian Busch <gizmore@wechall.net>";
	public $module_license = "MIT";
	public $module_priority = 50;
	
	public function gdoCached() { return false; }
	public function memCached() { return false; }
	public function sqlBuffered() { return true; } # Override with true or false for force.
	public function defaultEnabled() { return !$this->isSiteModule(); }
	public function isCoreModule() { return false; }
	public function isSiteModule() { return false; }
	public function isInstallable() { return true; }
	
	/**
	 * All modules have at least these dependencies.
	 * @TODO Would be nice to have no default dependencies, so a minimal install is possible.
	 */
	public function gdoDependencies()
	{
	    return [
	        'Core', 'Country', 'Language', 'Date', 'Mail',
	        'Table', 'User', 'Country', 'Javascript', 'UI',
	        'Session', 'File', 'CSS',
	    ];
	}
	
	/**
	 * A list of required dependencies, except core modules.
	 * Override this.
	 * @return string[]
	 */
	public function getDependencies() {}
	
//     /**
//      * A list of optional modules that enhance this one.
//      * Override this.
//      * @deprecated
// 	 * @return string[]
// 	 */
// 	public function getFriendencies() {}
	
    /**
	 * Skip these folders in unit tests using strpos.
	 * 
	 * @see Module_Tests
	 * @return string[]
	 */
	public function thirdPartyFolders() {}
	
	/**
	 * Get all module dependencies as moduleName.
	 * @return string[]
	 */
	public function dependencies()
	{
	    $coreDeps = $this->gdoDependencies();
	    if ($deps = $this->getDependencies())
	    {
	        return array_unique(array_merge($coreDeps, $deps));
	    }
	    else
	    {
	        return $coreDeps;
	    }
	}
	
	/**
	 * Provided theme name in module /thm/$themeName/ folder.
	 * @return string $themeName
	 */
	public function getTheme() {}
	
	/**
	 * GDO classes to install.
	 * @return string[]
	 */
	public function getClasses() {}
	
	/**
	 * Module config GDTs
	 * @return GDT[]
	 */
	public function getConfig() {}
	
	############
	### Info ###
	############
	/**
	 * Translated module name.
	 */
	public function displayName()
	{
		$name = $this->getName();
		$key = strtolower("module_{$name}");
		return Trans::hasKey($key) ? t($key) : $name;
	}
	
	public function getLowerName()
	{
	    return strtolower($this->getName());
	}
	
	public function displayModuleLicense()
	{
		return $this->getModuleLicense();
	}
	
	public function getModuleLicenseFilenames()
	{
	    return [
	        'LICENSE',
	    ];
	}
	
	/**
	 * Print license information.
	 * @TODO move to module gdo6-licenses
	 * @return string
	 */
	public function getModuleLicense()
	{
	    $all = '';
	    
	    $files = $this->getModuleLicenseFilenames();
	    
	    $div = '<hr/>';
	    
	    if ($descr = $this->getModuleDescription())
	    {
	    	$all .= "$descr\n$div";
	    	if ($files)
	    	{
	    		$gdo = 0; # gdo licenses
	    		foreach ($files as $file)
	    		{
	    			if ($this->filePath('LICENSE') === $this->filePath($file))
	    			{
	    				$gdo = 1;
	    			}
	    		}
	    		
	    		$count = count($files) - $gdo;
	    		if ($count)
	    		{
		    		$all .= "$count third-party-licenses involved:";
		    		$all .= "\n$div";
	    		}
	    	}
	    }
	    
	    if ($files)
	    {
	        foreach ($files as $i => $filename)
	        {
	            if ($i > 0)
	            {
	            	$all .= "\n$div";
	            }

	            $all .= GDT_Link::make()->
	            	labelRaw(Strings::substrFrom($filename, GDO_WEB_ROOT))->
	            	href($this->wwwPath($filename))->
	            	renderCell();
	            
       	        $filename = $this->filePath($filename);
        	    if (FileUtil::isFile($filename))
        	    {
        	        $all .= file_get_contents($filename);
        	    }
	        }
	    }
	    else
	    {
	        $all .= 'UNLICENSED / PROPERITARY';
	    }
        return $all;
	}

	public function displayModuleDescription() { return html($this->getModuleDescription()); }
	
	/**
	 * Module description is fetched from README.md by default.
	 * @return string
	 */
	public function getModuleDescription()
	{
		if ($readme = @file_get_contents($this->filePath('README.md')))
		{
			$matches = null;
			if (preg_match("/^#.*[\\r\\n]+(.*)[\\r\\n]/", $readme, $matches))
			{
				return $matches[1];
			}
		}
	}
	
	##############
	### Events ###
	##############
	public function onInit() {}
	public function onInitSidebar() {}
	public function onInstall() {}
	public function onWipe() {}
	public function onLoad() {}
	public function onLoadLanguage() {}
	public function onIncludeScripts() {}
	
	###########
	### GDO ###
	###########
	public function &gdoColumnsCache() { return Database::columnsS(self::class); } # Polymorph fix
	public function gdoTableName() { return 'gdo_module'; } # Polymorph fix
	public function gdoClassName() { return self::class; } # Polymorph fix
	public function gdoColumns()
	{
		return [
			GDT_AutoInc::make('module_id'),
			GDT_Name::make('module_name')->notNull()->unique(),
			GDT_Int::make('module_priority')->notNull()->unsigned()->initial($this->module_priority),
			GDT_Sort::make('module_sort'),
			GDT_Version::make('module_version')->notNull()->initial('0.00'),
			GDT_Checkbox::make('module_enabled')->notNull()->initial('0'),
		];
	}
	
	##############
	### Static ###
	##############
	/**
	 * @return static
	 */
	public static function instance() { return ModuleLoader::instance()->getModule(self::getNameS()); }
	
	private static $nameCache = [];
	public static function getNameS()
	{
	    if (isset(self::$nameCache[static::class]))
	    {
	        return self::$nameCache[static::class];
	    }
	    self::$nameCache[static::class] = $cache = strtolower(substr(self::gdoShortNameS(), 7));
	    return $cache;
	}
	
	##############
	### Getter ###
	##############
	public function getID() { return $this->getVar('module_id'); }
	public function getName() { return $this->getVar('module_name'); }
	public function getVersion() { return $this->getVar('module_version'); }
	public function isEnabled() { return $this->getVar('module_enabled'); }
	public function isInstalled() { return $this->isPersisted(); }
	public function getSiteName() { return sitename(); }
	public function env($key, $default=null) { return env("M_{$this->getName()}_{$key}", $default); }
	
	###############
	### Display ###
	###############
	public function render_fs_version() { return $this->module_version; }
	
	############
	### Href ###
	############
	public function href($methodName, $append='') { return href($this->getName(), $methodName, $append); }
	public function href_install_module() { return href('Admin', 'Install', '&module='.$this->getName()); }
	public function href_configure_module() { return href('Admin', 'Configure', '&module='.$this->getName()); }
	public function href_administrate_module() {}
	
	##############
	### Helper ###
	##############
	public function canUpdate() { return $this->module_version != $this->getVersion(); }
	public function canInstall() { return !$this->isPersisted(); }
	
	/**
	 * Filesystem path for a file within this module.
	 * @param string $path
	 * @return string
	 */
	public function filePath($path='') { return rtrim(GDO_PATH, '/') . $this->wwwPath($path, '/'); }
	
	/**
	 * Relative www path for a resource.
	 * @param string $path
	 * @return string
	 */
	public function wwwPath($path='', $webRoot=GDO_WEB_ROOT)
	{
	    return $webRoot . "GDO/{$this->getName()}/{$path}";
	}
	
	/**
	 * Filesystem path for a temp file. Absolute path to the gdo6/temp/{module}/ folder.
	 * @param string $filename appendix filename
	 * @return string the absolute path
	 */
	public function tempPath($path='')
	{
	    $base = Application::instance()->isUnitTests() ?
	       'temp_test' : 'temp';
	    $path = GDO_PATH . "{$base}/" . $this->getName() .'/' . $path;
	    $dir = Strings::rsubstrTo($path, "/");
	    FileUtil::createDir($dir);
	    return $path;
	}
	
	#################
	### Templates ###
	#################
	public function php($path, array $tVars=null)
	{
	    return GDT_Template::php($this->getName(), $path, $tVars);
	}
	
	public function templateFile($file)
	{
	    return GDT_Template::file($this->getName(), $file);
	}
	
	/**
	 * @param string $file
	 * @param array $tVars
	 * @return GDT_Template
	 */
	public function templatePHP($path, array $tVars=null)
	{
		switch (Application::instance()->getFormat())
		{
			case 'json': return $tVars; # @TODO here is the spot to enable json for genereic templates.
			case 'html':
			default: return GDT_Template::make()->template($this->getName(), $path, $tVars);
		}
	}
	
	public function responsePHP($file, array $tVars=null)
	{
		switch (Application::instance()->getFormat())
		{
			case 'json': return GDT_Response::makeWith(GDT_JSON::make()->value($tVars));
			case 'html':
			default: return GDT_Response::makeWith($this->templatePHP($file, $tVars));
		}
	}
	
	public function error($key, array $args=null) { return GDT_Error::responseWith($key, $args, 405); }
	public function message($key, array $args=null) { return GDT_Success::responseWith($key, $args); }
	
	############
	### Init ###
	############
	public function __wakeup()
	{
	    $this->inited = false;
	    parent::__wakeup();
	}

	private $inited = false;
	
	public function initedModule()
	{
		$this->inited = true;
	}
	
	public function isInited()
	{
		return $this->inited;
	}
	
	public function loadLanguage($path)
	{
		Trans::addPath($this->filePath($path));
		return $this;
	}
	
	#####################
	### Module Config ###
	#####################
	/**
	 * @var GDT[]
	 */
	private $configCache = null;
	
	/**
	 * Get module configuration hashed and cached.
	 * @return GDT[]
	 */
	public function &buildConfigCache()
	{
	    if ($this->configCache === null)
	    {
	        if ($config = $this->getConfig())
	        {
                $this->configCache = [];
	            foreach ($config as $gdt)
	            {
	                $this->configCache[$gdt->name] = $gdt; #->gdo($this);
	            }
	        }
	        else
	        {
	            $this->configCache = false;
	        }
	    }
	    return $this->configCache;
	}
	
	public function &getConfigCache()
	{
	    if ($this->configCache === null)
	    {
    	    $this->buildConfigCache();
	    }
	    return $this->configCache;
	}
	
	private function configCacheKey()
	{
	    return $this->getName().'_config_cache';
	}
	
	public function getConfigMemcache()
	{
	    $key = $this->configCacheKey();
	    if (false === ($cache = Cache::get($key)))
	    {
	        $cache = $this->buildConfigCache();
	        Cache::set($key, $cache);
	    }
	    return $cache;
	}
	
	/**
	 * @param GDT
	 */
	public function getConfigColumn($key, $throwError=true)
	{
	    if (isset($this->configCache[$key]))
	    {
	        return $this->configCache[$key];
	    }
	    if ($throwError)
	    {
	        throw new GDOError('err_unknown_config', [$this->displayName(), html($key)]);
	    }
	}
	
	public function getConfigVar($key)
	{
	    if ($gdt = $this->getConfigColumn($key))
	    {
	        return $gdt->var;
	    }
	}
	
	public function getConfigValue($key)
	{
	    if ($gdt = $this->getConfigColumn($key))
	    {
	        return $gdt->getValue();
	    }
	}
	
	public function saveConfigVar($key, $var)
	{
	    $gdt = $this->getConfigColumn($key);
	    GDO_ModuleVar::createModuleVar($this, $gdt->initial($var));
	    Cache::remove('gdo_modules');
	}
	
	public function saveConfigValue($key, $value)
	{
	    GDO_ModuleVar::createModuleVar($this, $this->getConfigColumn($key)->initialValue($value));
	    Cache::remove('gdo_modules');
	}
	
	public function removeConfigVar($key)
	{
	    if ($gdt = $this->getConfigColumn($key))
	    {
	        $gdt->initial(null);
	        GDO_ModuleVar::removeModuleVar($this, $key);
	    }
	}
	
	public function increaseConfigVar($key, $by=1)
	{
	    $value = $this->getConfigValue($key);
	    return $this->saveConfigVar($key, $value + 1);
	}
	
	###################
	### User config ###
	###################
	/**
	 * Special URL for settings.
	 */
	public function getUserSettingsURL() {}
	
	/**
	 * Config that the user cannot change.
	 * @return GDT[]
	 */
	public function getUserConfig() {}
	
	/**
	 * User changeable settings.
	 * @return GDT[]
	 */
	public function getUserSettings() {}
	
	/**
	 * User changeable settings in a blob table. For e.g. signature.
	 * @return GDT[]
	 */
	public function getUserSettingBlobs() {}
	
	####################
	### Settings API ###
	####################
	public function setting($key)
	{
	    return $this->userSetting(GDO_User::current(), $key);
	}
	
	/**
	 * 
	 * @param GDO_User $user
	 * @param string $key
	 * @return GDT
	 */
	public function userSetting(GDO_User $user, $key)
	{
	    if ($gdt = $this->getSetting($key))
	    {
	    	$gdt->gdo($user);
    	    $settings = $this->loadUserSettings($user);
    	    $var = isset($settings[$key]) ? $settings[$key] : $gdt->initial;
   	        return $gdt->initial($var);
	    }
	}
	
	public function settingVar($key)
	{
	    return $this->userSettingVar(GDO_User::current(), $key);
	}
	
	public function settingValue($key)
	{
	    return $this->userSettingValue(GDO_User::current(), $key);
	}
	
	public function userSettingVar(GDO_User $user, $key)
	{
	    return $this->userSetting($user, $key)->var;
	}
	
	public function userSettingValue(GDO_User $user, $key)
	{
	    $gdt = $this->userSetting($user, $key);
	    return $gdt->getValue();
	}
	
	public function saveSetting($key, $var)
	{
	    return self::saveUserSetting(GDO_User::current(), $key, $var);
	}
	
	public function saveUserSetting(GDO_User $user, $key, $var)
	{
	    $gdt = $this->getSetting($key);
	    if (!$user->getID())
	    {
	        return $gdt;
	    }
	    $data = [
	        'uset_user' => $user->getID(),
	        'uset_name' => $key,
	        'uset_value' => $var,
	    ];
	    $entry = ($gdt instanceof GDT_Text) ?
	       GDO_UserSettingBlob::blank($data) :
	       GDO_UserSetting::blank($data);
	    $entry->replace();
	    $user->tempUnset('gdo_setting');
	    $user->recache();
	    return $gdt;
	}
	
	public function increaseSetting($key, $by=1)
	{
	    return $this->increaseUserSetting(GDO_User::current(), $key, $by);
	}
	
	public function increaseUserSetting(GDO_User $user, $key, $by=1)
	{
	    return $this->saveUserSetting(
	        $user, $key, $this->userSettingVar($user, $key) + $by);
	}
	
	# Cache
	/**
	 * @var GDT[]
	 */
	private $userConfigCache = null;
	
	public function &getSettingsCache()
	{
	    if ($this->userConfigCache === null)
	    {
	        $this->buildSettingsCache();
	    }
	    return $this->userConfigCache;
	}
	
	public function hasSetting($key)
	{
	    $this->buildSettingsCache();
	    return isset($this->userConfigCache[$key]);
	}

	private function getSetting($key)
	{
	    if (isset($this->userConfigCache[$key]))
	    {
    	    return $this->userConfigCache[$key];
	    }
	    else
	    {
	        throw new GDOError('err_unknown_user_setting', [$this->displayName(), html($key)]);
	    }
	}
	
	public function &buildSettingsCache()
	{
	    if ($this->userConfigCache === null)
	    {
    	    $this->userConfigCache = [];
    	    if ($config = $this->getUserConfig())
    	    {
    	        foreach ($config as $gdt)
    	        {
    	            $gdt->editable(false);
    	            $this->userConfigCache[$gdt->name] = $gdt;
    	        }
    	    }
    	    if ($config = $this->getUserSettings())
    	    {
    	        foreach ($config as $gdt)
    	        {
    	            $this->userConfigCache[$gdt->name] = $gdt;
    	        }
    	    }
    	    if ($config = $this->getUserSettingBlobs())
    	    {
    	        foreach ($config as $gdt)
    	        {
    	            $this->userConfigCache[$gdt->name] = $gdt;
    	        }
    	    }
	    }
	    return $this->userConfigCache;
	}
	
	private function loadUserSettings(GDO_User $user)
	{
	    if (null === ($settings = $user->tempGet('gdo_setting')))
	    {
	        $settings = self::loadUserSettingsB($user);
	        $user->tempSet('gdo_setting', $settings);
// 	        $user->recache();
	    }
	    return $settings;
	}
	
	private function loadUserSettingsB(GDO_User $user)
	{
	    if (!$user->isPersisted())
	    {
	        return [];
	    }
	    return array_merge(
	        GDO_UserSetting::table()->select('uset_name, uset_value')->
	           where("uset_user={$user->getID()}")->exec()->fetchAllArray2dPair(),
	        GDO_UserSettingBlob::table()->select('uset_name, uset_value')->
	           where("uset_user={$user->getID()}")->exec()->fetchAllArray2dPair()
	    );
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
	    $methods = $this->getMethods(false);
	    foreach ($methods as $method)
	    {
	        if (strcasecmp($methodName, $method->gdoShortName()) === 0)
	        {
	            return $method;
	        }
	    }
	}
	
	/**
	 * Get a method by name. Case insensitive.
	 * @param string $methodName
	 * @return Method
	 */
	public function getMethodByName($methodName)
	{
	    $files = scandir($this->filePath('Method'));
	    foreach ($files as $file)
	    {
	        $file = substr($file, 0, -4);
	        if (strcasecmp($methodName, $file) === 0)
	        {
	            $className = "\\GDO\\{$this->getName()}\\Method\\{$file}";
	            $method = call_user_func([$className, 'make']);
	            return $method;
	        }
	    }
	}
	
	public function getMethodNames($withPermission=true)
	{
	    $methods = $this->getMethods($withPermission);
	    return array_map(function(Method $method) {
	        return $method->gdoShortName();
	    }, $methods);
	}
	
	/**
	 * @param boolean $withPermission
	 * @return Method[]
	 */
	public function getMethods($withPermission=true)
	{
	    $methods = scandir($this->filePath('Method'));
	    $methods = array_map(function($file) {
	        return substr($file, 0, -4);
	    }, $methods);
	    $methods = array_filter($methods, function($file) {
            return !!$file;
        });
        $methods = array_map(function($file) {
            $className = "\\GDO\\{$this->getName()}\\Method\\{$file}";
            return call_user_func([$className, 'make']);
        }, $methods);
        if ($withPermission)
        {
            $methods = array_filter($methods, function(Method $method) {
//                 try
//                 {
                    return $method->hasUserPermission(GDO_User::current());
//                 }
//                 catch (\Throwable $ex)
//                 {
//                     return false;
//                 }
            });
        }
        return $methods;
	}
	
	##############
	### Assets ###
	##############
	
	/**
	 * nocache appendix
	 * @var string
	 */
	private static $_NC;

	/**
	 * Get the cache poisoner.
	 * Base is gdo revision string.
	 * Additionally a cache clear triggers an increase of the assets version.
	 * @return string
	 */
	public function nocacheVersion()
	{
	    if (!self::$_NC)
	    {
	        $v = Module_Core::GDO_REVISION;
	        $av = Module_Core::instance()->cfgAssetVersion();
	        self::$_NC = "_v={$v}&_av={$av}";
	    }
        return self::$_NC;
	}
	
	public function addBowerJS($path)
	{
	    return $this->addJS('bower_components/'.$path);
	}
	
	public function addJS($path)
	{
	    return Javascript::addJS(
	        $this->wwwPath($path . '?' . $this->nocacheVersion()));
	}
	
	public function addBowerCSS($path)
	{
	    return $this->addCSS('bower_components/'.$path);
	}
	
	public function addCSS($path)
	{
	    return Website::addCSS($this->wwwPath($path.'?'.$this->nocacheVersion()));
	}

	public function prefetch($path, $type)
	{
	    $v = $this->nocacheVersion();
	    $href = $this->wwwPath($path.'?'.$v);
	    Website::addPrefetch($href, $type);
	}
}
