<?php
namespace GDO\Core;

use GDO\Util\Common;
use GDO\Session\GDO_Session;

/**
 * The application can control main behaviour settings.
 * It holds the global time variables for exact time measurements in games or replays.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.0.0
 */
class Application
{
	const HTML = 'html';
	const JSON = 'json';
	const CLI = 'cli';
	const XML = 'xml';
	
	/**
	 * @return self
	 */
	public static function instance() { return self::$instance; }
	private static $instance;
	
	############
	### Time ###
	############
	public static $TIME;
	public static $MICROTIME;
	
	/**
	 * Move forward in time.
	 * Or set to a patched value for replays or debugging reasons.
	 */
	public static function updateTime($microtime=null)
	{
	    $microtime = $microtime === null ? microtime(true) : $microtime;
	    self::$TIME = (int) $microtime;
	    self::$MICROTIME = $microtime;
	}
	
	################
	### Instance ###
	################
	public $loader;
	public function __construct()
	{
		self::$instance = $this;
        ini_set('date.timezone', 'UTC');
		date_default_timezone_set('UTC');
		
		if (PHP_SAPI !== 'cli')
		{
			$this->initThemes();
		}
		else
		{
			$this->themes = ['default'];
		}

        $this->loader = ModuleLoader::instance() ?
            ModuleLoader::instance() :
            new ModuleLoader(GDO_PATH . 'GDO/');
	}
	
	public function __destruct()
	{
	    if (class_exists('GDO\\Core\\Logger', false))
	    {
    		Logger::flush();
	    }
	}
	
	public function isWindows() { return defined('PHP_WINDOWS_VERSION_MAJOR'); }
	
	public function isUnitTests() { return false; }
	
	public function isCronjob() { return false; }
	
	/**
	 * @return \GDO\Core\Method
	 */
	public function getMethod()
	{
	    static $method = null;
	    if ($method === null)
	    {
    	    if ($module = ModuleLoader::instance()->getModule(mo()))
    	    {
        	    $method = $module->getMethod(me());
    	    }
    	    else
    	    {
//     	        $method = false;
    	    }
	    }
	    return $method;
	}
	
	################
	### Override ###
	################
	public function isCLI() { return PHP_SAPI === 'cli'; }
	public function isInstall() { return false; }
	public function isWebsocket() { return false; }
	public function allowJavascript() { return !isset($_REQUEST['disableJS']); }
	
	##############
	### Format ###
	##############
	public function isAjax() { return !!@$_GET['ajax']; }
	public function isXML() { return $this->getFormat() === self::XML; }
	public function isHTML() { return $this->getFormat() === self::HTML; }
	public function isJSON() { return $this->getFormat() === self::JSON; }
	public function getFormat()
	{
	    if ($this->isCLI())
	    {
	        return self::CLI;
	    }
	    return Common::getRequestString('_fmt', self::HTML);
	}
	
	##############
	### Themes ###
	##############
	private $themes = GDO_THEMES;
	public function getThemes() { return $this->themes; }
	public function hasTheme($theme) { return isset($this->themes[$theme]); }
	public function initThemes()
	{
	    if (!$this->isInstall())
	    {
    	    if (GDO_Session::get('theme_name'))
    	    {
    	        $this->themes = GDO_Session::get('theme_chain');
    	    }
    	    $this->themes = explode(',', $this->themes);
    	    $this->themes = array_combine($this->themes, $this->themes);
	    }
	    return $this;
	}

}

# setup current time.
Application::updateTime();
