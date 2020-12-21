<?php
namespace GDO\Core;

use GDO\Util\Common;
use GDO\Session\GDO_Session;

/**
 * The application can control main behaviour settings.
 * It holds the global time variables for exact time measurements in games or replays.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
class Application
{
	const HTML = 'html';
	const JSON = 'json';
	
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
	 */
	public static function updateTime()
	{
	    self::$MICROTIME = microtime(true);
	    self::$TIME = (int) self::$MICROTIME;
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
		$this->initThemes();
		$this->loader = new ModuleLoader(GDO_PATH . 'GDO/');
	}
	
	public function __destruct()
	{
		Logger::flush();
	}
	
	public function isWindows() { return defined('PHP_WINDOWS_VERSION_MAJOR'); }
	
	public function isUnitTests() { return false; }
	
	/**
	 * @return \GDO\Core\Method
	 */
	public function getMethod() { return method(Common::getRequestString('mo', GWF_MODULE), Common::getRequestString('me', GWF_METHOD)); }
	
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
	public function isAjax() { return isset($_GET['ajax']); }
	public function isHTML() { return (!$this->isCLI()) && $this->getFormat() === self::HTML; }
	public function isJSON() { return $this->getFormat() === self::JSON; }
	public function getFormat() { return Common::getRequestString('fmt', self::HTML); }

	##############
	### Themes ###
	##############
	private $themes = GWF_THEMES;
	public function getThemes() { return $this->themes; }
	public function hasTheme($theme) { return isset($this->themes[$theme]); }
	public function initThemes()
	{
	    if (GDO_Session::get('theme_name'))
	    {
	        $this->themes = GDO_Session::get('theme_chain');
	    }
	    $this->themes = explode(',', $this->themes);
	    $this->themes = array_combine($this->themes, $this->themes);
	}

}

Application::updateTime();
