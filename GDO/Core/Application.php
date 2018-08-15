<?php
namespace GDO\Core;
use GDO\Util\Common;
use GDO\User\GDO_Session;
/**
 * The application can control main behaviour settings.
 * 
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class Application
{
	const HTML = 'html';
	const JSON = 'json';
	
	/**
	 * @return \GDO\Core\Application
	 */
	public static function instance() { return self::$instance; }
	private static $instance;
	
	################
	### Instance ###
	################
	private $loader;
	public function __construct()
	{
		self::$instance = $this;
		$this->loader = new ModuleLoader(GWF_PATH . 'GDO/');
	}
	public function __destruct()
	{
		Logger::flush();
		GDO_Session::commit();
	}
	
	public function isWindows() { return defined('PHP_WINDOWS_VERSION_MAJOR'); }
	
	################
	### Override ###
	################
	public function isCLI() { return false; }
	public function isInstall() { return false; }

	##############
	### Format ###
	##############
	public function isAjax() { return isset($_GET['ajax']); }
	public function isHTML() { return $this->getFormat() === self::HTML; }
	public function isJSON() { return $this->getFormat() === self::JSON; }
	public function getFormat() { return Common::getGetString('fmt', self::HTML); }

	##############
	### Themes ###
	##############
	private $themes = GWF_THEMES;
	public function getThemes() { return is_array($this->themes) ? $this->themes : explode(',', $this->themes); }
	public function setThemes(array $themes) { $this->themes = $themes; return $this; }
}
