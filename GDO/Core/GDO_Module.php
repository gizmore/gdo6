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
 * 
 * @author gizmore
 * @since 1.00
 * @version 6.10
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
	public function gdoDependencies() { return ['Core', 'Language', 'Table', 'User', 'Country']; }
	
	/**
	 * @return string[]
	 */
	public function getDependencies() { return []; }
	
	/**
	 * @return string[]
	 */
	public function dependencies() { return array_unique(array_merge($this->gdoDependencies(), $this->getDependencies())); }
	
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
				$gdoType->gdo($this)->var($this->getConfigVar($gdoType->name));
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
		return $this->getConfigColumn($key)->initial;
	}
	
	public function getConfigValue($key)
	{
		$column = $this->getConfigColumn($key);
		return $column->toValue($column->initial);
	}
	
	public function saveConfigVar($key, $var)
	{
		GDO_ModuleVar::createModuleVar($this, $this->getConfigColumn($key)->initial($var));
		Cache::remove('gdo_modules');
	}
	
	public function saveConfigValue($key, $value)
	{
		GDO_ModuleVar::createModuleVar($this, $this->getConfigColumn($key)->value($value));
		Cache::remove('gdo_modules');
	}
	
	public function removeConfigVar($key)
	{
		$this->getConfigColumn($key)->initial(null);
		GDO_ModuleVar::removeModuleVar($this, $key);
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
	public function isInstalled() { return $this->isPersisted(); }
	public function getSiteName() { return sitename(); }
	
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
	public function __wakeup() { $this->inited = false; } # TODO: wakeup knows that language and settings are also memcached soon? :)
	private $inited = false;
	public function initModule()
	{
		if (!$this->inited)
		{
			$this->registerSettings();
			if ($this->isEnabled())
			{
				$this->onInit();
// 				GDT_Hook::initModule($this);
			}
		}
	}
	
	public function initedModule()
	{
		$this->inited = true;
	}
	
	public function isInited()
	{
		return $this->inited;
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
	public function getUserSettingsURL(){}
	
	/**
	 * @return GDT[]
	 */
	public function getUserSettingBlobs(){}
	public function registerSettings()
	{
		$this->_registerSettings($this->getUserConfig());
		$this->_registerSettings($this->getUserSettings());
		$this->_registerSettings($this->getUserSettingBlobs(), true);
	}
	private function _registerSettings(array $settings=null, $blob=false)
	{
		if ($settings)
		{
			foreach ($settings as $setting)
			{
				$blob ? GDO_UserSettingBlob::register($setting) : GDO_UserSetting::register($setting);
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
		return method($this->getName(), $methodName);
	}
	
	##############
	### Assets ###
	##############
	private static $_NC;
	public function nocacheVersion() { if (!self::$_NC) self::$_NC = "v={$this->getVersion()}&vc=".Module_Core::instance()->cfgAssetVersion(); return self::$_NC; }
	public function addBowerJavascript($path) { return $this->addJavascript('bower_components/'.$path); }
	public function addJavascript($path) { return Javascript::addJavascript($this->wwwPath($path.'?'.$this->nocacheVersion())); }
	public function addBowerCSS($path) { return $this->addCSS('bower_components/'.$path); }
	public function addCSS($path) { return Website::addCSS($this->wwwPath($path.'?'.$this->nocacheVersion())); }

}
