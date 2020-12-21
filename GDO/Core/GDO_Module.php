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
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_UserSettingBlob;
use GDO\User\GDO_User;
use GDO\DB\GDT_Text;
use GDO\Tests\Module_Tests;

/**
 * GDO base module class.
 * 
 * @author gizmore
 * @version 6.10
 * @since 1.00
 */
class GDO_Module extends GDO
{
	################
	### Override ###
	################
	public $module_version = "6.10";
	public $module_author = "Christian Busch <gizmore@wechall.net>";
	public $module_license = "MIT";
	public $module_priority = 50;
	
	public function gdoCached() { return false; }
	public function memCached() { return false; }
	public function defaultEnabled() { return !$this->isSiteModule(); }
	public function isCoreModule() { return false; }
	public function isSiteModule() { return false; }
	public function isInstallable() { return true; }
	public function gdoDependencies() { return ['Core', 'Country', 'Language', 'Table', 'User', 'Country']; }
	
	/**
	 * @return string[]
	 */
	public function getDependencies() {}
	
	/**
	 * @return string[]
	 */
	public function getBlockedModules() {}
	private $blocked = false;
	public function isBlocked() { return $this->blocked; }
	public function setBlocked() { $this->blocked = true; }

	/**
	 * Skip these folders in unit tests using strpos.
	 * 
	 * @see Module_Tests
	 * @return string[]
	 */
	public function thirdPartyFolders() {}
	
	/**
	 * @return string[]
	 */
	public function dependencies()
	{
	    if ($deps = $this->getDependencies())
	    {
    	    return array_unique(array_merge($this->gdoDependencies(), $deps));
	    }
	    else
	    {
	        return $this->gdoDependencies();
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
	public function displayName()
	{
		$name = $this->getName();
		$key = 'module_' . strtolower($name);
		return Trans::hasKey($key) ? t($key) : $name;
	}
	
	public function displayModuleDescription() { return html($this->getModuleDescription()); }
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
		return '';
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
	public function gdoColumnsCache() { return Database::columnsS(self::class); } # Polymorph fix
	public function gdoTableName() { return 'gdo_module'; } # Polymorph fix
	public function gdoClassName() { return self::class; } # Polymorph fix
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
	 * @return static
	 */
	public static function instance() { return ModuleLoader::instance()->getModule(self::getNameS()); }
	
	private static $nameCache = [];
	public static function getNameS()
	{
	    if ($cache = @self::$nameCache[static::class])
	    {
	        return $cache;
	    }
	    self::$nameCache[static::class] = $cache = substr(self::gdoShortNameS(), 7);
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
	public function filePath($path='') { return GDO_PATH.$this->wwwPath($path); }
	
	/**
	 * Relative www path for a resource.
	 * @param string $path
	 * @return string
	 */
	public function wwwPath($path='') { return "GDO/{$this->getName()}/$path"; }
	
	/**
	 * Filesystem path for a temp file. Absolute path to the gdo6/temp/{module}/ folder.
	 * @param string $filename appendix filename
	 * @return string the absolute path
	 */
	public function tempPath($filename='') { return GDO_PATH . 'temp/' . $this->getName() .'/' . $filename; }
	
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
			case 'json': return $tVars;
			case 'html':
			default: return GDT_Template::php($this->getName(), $file, $tVars);
		}
	}
	
	public function responsePHP($file, array $tVars=null)
	{
		switch (Application::instance()->getFormat())
		{
			case 'json': return GDT_Response::makeWith(GDT_JSON::make()->value($tVars));
			case 'html':
			default: return GDT_Template::responsePHP($this->getName(), $file, $tVars);
		}
	}
	
	
	public function templateFile($file) { return GDT_Template::file($this->getName(), $file); }
	public function error($key, array $args=null) { return GDT_Error::responseWith($key, $args); }
	public function message($key, array $args=null) { return GDT_Success::responseWith($key, $args); }
	
	############
	### Init ###
	############
// 	public function __wakeup() { $this->inited = false; self::$COUNT++; }

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
	public function buildConfigCache()
	{
	    if ($this->configCache === null)
	    {
	        if ($config = $this->getConfig())
	        {
                $this->configCache = [];
	            foreach ($config as $gdt)
	            {
	                $this->configCache[$gdt->name] = $gdt; //->gdo($this);
	            }
	        }
	        else
	        {
	            $this->configCache = false;
	        }
	    }
	    return $this->configCache;
	}
	
	public function getConfigCache()
	{
	    return $this->buildConfigCache();
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
	public function getConfigColumn($key)
	{
	    if (isset($this->configCache[$key]))
	    {
	        return $this->configCache[$key];
	    }
	    Website::error('err_unknown_config', [$this->displayName(), html($key)]);
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
	        return $gdt->toValue($gdt->initial);
	    }
	}
	
	public function saveConfigVar($key, $var)
	{
	    GDO_ModuleVar::createModuleVar($this, $this->getConfigColumn($key)->initial($var));
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
    	    $settings = $this->loadUserSettings($user);
    	    if (isset($settings[$key]))
    	    {
    	        $gdt->initial($settings[$key]);
    	    }
    	    return $gdt;
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
	    return $gdt->toValue($gdt->var);
	}
	
	public function saveSetting($key, $var)
	{
	    return self::saveUserSetting(GDO_User::current(), $key, $var);
	}
	
	public function saveUserSetting(GDO_User $user, $key, $var)
	{
	    $gdt = $this->getSetting($key);
	    $data = [
	        'uset_user' => $user->getID(),
	        'uset_name' => $key,
	        'uset_value' => $var,
	    ];
	    $entry = ($gdt instanceof GDT_Text) ? GDO_UserSettingBlob::blank($data) : GDO_UserSetting::blank($data);
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
	    return $this->saveUserSetting($user, $key, $this->userSettingVar($user, $key) + $by);
	}
	
	# Cache
	/**
	 * @var GDT[]
	 */
	private $userConfigCache = null;
	
	public function getSettingsCache()
	{
	    return $this->buildSettingsCache();
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
	
	public function buildSettingsCache()
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
	        $user->recache();
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
	        GDO_UserSetting::table()->select('uset_name, uset_value')->where("uset_user={$user->getID()}")->exec()->fetchAllArray2dPair(),
	        GDO_UserSettingBlob::table()->select('uset_name, uset_value')->where("uset_user={$user->getID()}")->exec()->fetchAllArray2dPair()
	    );
	}
	
	##############
	### Method ###
	##############
	/**
	 * @deprecated Use Method::make() instead.
	 * @param string $methodName
	 * @return Method
	 */
	public function getMethod($methodName)
	{
		return method($this->getName(), $methodName);
	}
	
	##############
	### Assets ###
	##############
	private static $_NC; # nocache appendix
	public function nocacheVersion() { if (!self::$_NC) self::$_NC = "v={$this->getVersion()}&vc=".Module_Core::instance()->cfgAssetVersion(); return self::$_NC; }
	public function addBowerJavascript($path) { return $this->addJavascript('bower_components/'.$path); }
	public function addJavascript($path) { return Javascript::addJavascript($this->wwwPath($path.'?'.$this->nocacheVersion())); }
	public function addBowerCSS($path) { return $this->addCSS('bower_components/'.$path); }
	public function addCSS($path) { return Website::addCSS($this->wwwPath($path.'?'.$this->nocacheVersion())); }

}
