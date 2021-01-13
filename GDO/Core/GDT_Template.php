<?php
namespace GDO\Core;
use Exception;
use GDO\UI\WithLabel;
use GDO\Util\Strings;
use GDO\Language\Trans;
use GDO\User\GDO_User;
use GDO\File\FileUtil;
/**
 * GWF Template Engine.
 * Very cheap / basic
 *
 * - There are php and static file templates.
 *
 * - Themes is an array, so you can have cascading override.
 * 
 * - @TODO: Cache template files and use eval on them. maybe that's a tad faster than Filesystem.
 * 
 * @author gizmore
 * @version 6.05
 * @since 3.00
 */
class GDT_Template extends GDT
{
	use WithLabel;
	
	public static $THEMES = [];
	public static function themeNames() { return array_keys(self::$THEMES); }
	public static function registerTheme($theme, $path) { self::$THEMES[$theme] = $path; }
	
	############
	### Base ###
	############
	public function defaultLabel() { return $this->noLabel(); }
	public function htmlClass()
	{
		$class = parent::htmlClass();
		$class .= "-{$this->templateModule}-".Strings::rsubstrFrom(Strings::substrTo($this->templatePath, '.'), '/');
		return $class;
	}
	
	public function render() { return $this->renderTemplate(); }
	public function renderCell() { return $this->renderTemplate(); }
	public function renderForm() { return $this->renderTemplate(); }
	public function renderFilter($f) { return $this->renderTemplate($f); }
	public function renderTemplate($f=null)
	{
		$tVars = ['field'=>$this, 'f' => $f];
		$tVars = $this->templateVars ? array_merge($this->templateVars, $tVars) : $tVars;
		return self::php($this->templateModule, $this->templatePath, $tVars);
	}
	############
	### Type ###
	############
	public $templateModule;
	public $templatePath;
	public $templateVars;
	public function template($moduleName, $path, array $tVars=null)
	{
		$this->templateModule = $moduleName;
		$this->templatePath = $path;
		$this->templateVars = $tVars;
		return $this;
	}
	
	##############
	### Header ###
	##############
	public $templateModuleHead;
	public $templatePathHead;
	public $templateVarsHead;
	public function templateHead($moduleName, $path, array $tVars=null)
	{
		$this->templateModuleHead = $moduleName;
		$this->templatePathHead = $path;
		$this->templateVarsHead = $tVars;
		return $this;
	}

	public function renderHeader()
	{
		if (!$this->templateModuleHead)
		{
			return $this->displayLabel();
		}
		$tVars = ['field'=>$this];
		$tVars = $this->templateVarsHead ? array_merge($this->templateVarsHead, $tVars) : $tVars;
		return self::php($this->templateModuleHead, $this->templatePathHead, $tVars);
	}
	
	##############
	### Engine ###
	##############
	public static $CALLS = 0;
	
	public static function phpUser(GDO_User $user, $moduleName, $path, array $tVars=null)
	{
		$old = Trans::$ISO;
		Trans::$ISO = $user->getLangISO();
		$result = self::php($moduleName, $path, $tVars);
		Trans::$ISO = $old;
		return $result;
	}
	
	public static function php($moduleName, $path, array $tVars=null)
	{
		try
		{
			ob_start();
			self::$CALLS++;
			$path = self::getPath($moduleName, $path);
			if (isset($tVars))
			{
				foreach ($tVars as $__key => $__value)
				{
					$$__key = $__value; # make tVars locals for the template
				}
			}
			include $path;
			return ob_get_contents();
		}
		catch (Exception $e)
		{
			Logger::logException($e);
			return ob_get_contents() . Debug::backtraceException($e, Application::instance()->isHTML(), ' (TPL)');
		}
		finally
		{
			ob_end_clean();
		}
	}
	
	public static function responsePHP($moduleName, $path, array $tVars=null)
	{
		return GDT_Response::makeWith(self::make()->template($moduleName, $path, $tVars));
	}
	
	public static function file($moduleName, $path)
	{
		self::$CALLS++;
		$path = self::getPath($moduleName, $path);
		return file_get_contents($path);
	}

	#########################
	### Path substitution ###
	#########################
	private static $PATHES = [];
	/**
	 * Get the Path for the GWF Design if the file exists
	 * @param string $path templatepath
	 * @return string
	 */
	private static function getPath($moduleName, $path)
	{
	    $key = Trans::$ISO.$moduleName.$path;
	    if (!isset(self::$PATHES[$key]))
	    {
	        self::$PATHES[$key] = self::getPathB($moduleName, $path);
	    }
	    return self::$PATHES[$key];
	}
	
	private static function getPathB($moduleName, $path)
	{
	    $isos = array_unique(['', '_'.Trans::$ISO, '_'.GWF_LANGUAGE, '_en']);
		
		$path12 = Strings::rsubstrTo($path, '.', $path);
		$path13 = Strings::rsubstrFrom($path, '.', '');

		# Try themes first
		foreach ($isos as $iso)
		{
    		foreach (Application::instance()->getThemes() as $theme)
			{
    			if (isset(self::$THEMES[$theme]))
    			{
    				$path1 = $path12 . $iso . '.' . $path13;
   					$path1 = self::$THEMES[$theme]."/$moduleName/tpl/$path1";
   					if (FileUtil::isFile($path1))
    				{
    					return $path1;
    				}
    			}
			}
		}
		
		foreach ($isos as $iso)
		{
			$path1 = $path12 . $iso . '.' . $path13;
			$path1 = GDO_PATH . "GDO/$moduleName/tpl/$path1";
			if (FileUtil::isFile($path1))
			{
				return $path1;
			}
		}

		// Try module file on module templates.
		return GDO_PATH . "GDO/$moduleName/tpl/$path";
	}
	
}
