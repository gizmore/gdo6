<?php
namespace GDO\Core;

use GDO\Util\Common;
use GDO\Session\GDO_Session;

/**
 * The application can control main behaviour settings.
 * It holds the global time variables for exact time measurements in games or replays.
 * Holds registered themes.
 * 
 * @author gizmore
 * @version 6.11.1
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
	/**
	 * @var ModuleLoader
	 */
	public $loader;

	/**
	 * Instanciate a gdo6 application.
	 */
	public function __construct()
	{
		self::$instance = $this;
        ini_set('date.timezone', 'UTC');
		date_default_timezone_set('UTC');
        $this->loader = ModuleLoader::instance() ?
            ModuleLoader::instance() :
            new ModuleLoader(GDO_PATH . 'GDO/');
		$this->themes = GDO_THEMES;
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
	 * Check if normal website (not install)
	 * @return boolean
	 */
	public function isWebServer()
	{
		return ( (!$this->isWebsocket()) &&
		         (!$this->isCLI()) &&
		         (!$this->isInstall()) );
	}
	
	/**
	 * @return \GDO\Core\Method
	 */
	public function getMethod()
	{
	    static $method;
	    if ($method === null)
	    {
    	    if ($module = ModuleLoader::instance()->getModule(mo(), false))
    	    {
        	    $method = $module->getMethod(me());
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
	public function isAjax() { return !!@$_REQUEST['_ajax']; }
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
	public function getThemes()
	{
		if (is_string($this->themes))
		{
			$this->themes = explode(',', $this->themes);
			$this->themes = array_combine($this->themes, $this->themes);
		}
		return $this->themes;
	}
	
	public function hasTheme($theme) { return isset($this->themes[$theme]); }
	public function initThemes()
	{
	    if ( (!$this->isInstall()) && (!$this->isCLI()) )
	    {
	    	if (class_exists('GDO\\Session\\GDO_Session', false))
	    	{
	    	    if (GDO_Session::get('theme_name'))
	    	    {
	    	        $this->themes = GDO_Session::get('theme_chain');
	    	    }
	    	}
	    }
	    return $this;
	}

}

# setup current time.
Application::updateTime();
