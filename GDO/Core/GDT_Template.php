<?php
namespace GDO\Core;
use Exception;
use GDO\UI\WithLabel;
use GDO\Util\Strings;
use GDO\Language\Trans;
use GDO\User\GDO_User;
/**
 * GWF Template Engine.
 * Very cheap / basic
 *
 * - There are php and static file templates.
 *
 * - Themes is an array, so you can have cascading override.
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
	public function renderFilter() { return $this->renderTemplate(); }
	public function renderHeader() { return $this->displayLabel(); }
	public function renderTemplate()
	{
		$tVars = ['field'=>$this];
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
			if ($tVars)
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
	/**
	 * Get the Path for the GWF Design if the file exists
	 * @param string $path templatepath
	 * @return string
	 */
	private static function getPath($moduleName, $path)
	{
		$isos = array_unique(['_'.Trans::$ISO, '_'.GWF_LANGUAGE, '_en', '']);

		# Try themes first
		foreach (Application::instance()->getThemes() as $theme)
		{
			foreach ($isos as $iso)
			{
				$path1 = Strings::rsubstrTo($path, '.', $path) . $iso . '.' . Strings::rsubstrFrom($path, '.', '');
				if (isset(self::$THEMES[$theme]))
				{
					$path1 = self::$THEMES[$theme]."/$moduleName/tpl/$path1";
					if (is_file($path1))
					{
						return $path1;
					}
				}
			}
		}
		
		foreach ($isos as $iso)
		{
			$path1 = Strings::rsubstrTo($path, '.', $path) . $iso . '.' . Strings::rsubstrFrom($path, '.', '');
			$path1 = GWF_PATH . "GDO/$moduleName/tpl/$path1";
			if (is_file($path1))
			{
				return $path1;
			}
		}

		// Try module file on module templates.
		return GWF_PATH . "GDO/$moduleName/tpl/$path";
	}
}
