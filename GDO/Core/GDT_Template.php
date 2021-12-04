<?php
namespace GDO\Core;

use GDO\UI\WithLabel;
use GDO\Util\Strings;
use GDO\Language\Trans;
use GDO\User\GDO_User;
use GDO\File\FileUtil;

/**
 * GDO Template Engine and GDT implementation.
 *
 * Automatically appends fields from tVars.
 *
 * - There are php and static file templates.
 * - Themes is an array, so you can always override with your theme.
 * - Templates add WithFields for GDT $templateVars ($tVars). It can render with JSON.
 *
 * @author gizmore
 * @version 6.11.0
 * @since 3.0.0
 */
class GDT_Template extends GDT
{
	use WithLabel;
	use WithFields;

	/**
	 *
	 * @var string[]
	 */
	public static $THEMES = [];

	public static function themeNames()
	{
		return array_keys(self::$THEMES);
	}

	public static function registerTheme($theme, $path)
	{
		self::$THEMES[$theme] = $path;
	}

	############
	### Base ###
	############
	public function defaultLabel()
	{
		return $this->noLabel();
	}

	public function htmlClass()
	{
		return parent::htmlClass() . "-{$this->templateModule}-" .
		Strings::rsubstrFrom(Strings::substrTo($this->templatePath, '.'), '/');
	}

	public function render()
	{
		return $this->renderTemplate();
	}

	public function renderJSON()
	{
		return $this->renderJSONFields();
	}
	
	public function renderCell()
	{
		return $this->renderTemplate();
	}

	public function renderForm()
	{
		return $this->renderTemplate();
	}

	public function renderCLI()
	{
		return strip_tags($this->renderTemplate());
	}

	public function renderFilter($f)
	{
		return $this->renderTemplate($f);
	}

	public function renderTemplate($f = null)
	{
		$tVars = [
			'field' => $this,
			'f' => $f
		];
		$tVars = $this->templateVars ? array_merge($this->templateVars, $tVars) : $tVars;
		return self::php($this->templateModule, $this->templatePath, $tVars);
	}

	# ###########
	# ## Type ###
	# ###########
	public $templateModule;

	public $templatePath;

	public $templateVars;

	public function template($moduleName, $path, array $tVars = null)
	{
		$this->templateModule = $moduleName;
		$this->templatePath = $path;
		$this->templateVars = $tVars;
		
		# HTML renders directly.
		# JSON/CLI/XML render only the fields.
		if (!Application::instance()->isHTML())
		{
			GDT_Response::make()->addFields($tVars);
		}
		return $this;
	}

	# #############
	# ## Header ###
	# #############
	public $templateModuleHead;

	public $templatePathHead;

	public $templateVarsHead;

	public function templateHead($moduleName, $path, array $tVars = null)
	{
		$this->templateModuleHead = $moduleName;
		$this->templatePathHead = $path;
		$this->templateVarsHead = $tVars;
		return $this;
	}

	public function renderHeader()
	{
		if ( !$this->templateModuleHead)
		{
			return $this->displayLabel();
		}
		$tVars = [
			'field' => $this
		];
		$tVars = $this->templateVarsHead ? array_merge($this->templateVarsHead,
		$tVars) : $tVars;
		return self::php($this->templateModuleHead, $this->templatePathHead,
		$tVars);
	}

	# #############
	# ## Engine ###
	# #############
	public static $CALLS = 0;

	# Performance counter

	/**
	 * Include a template for a user.
	 * Sets/Wraps locale ISO for the template call.
	 *
	 * @param GDO_User $user
	 * @param string $moduleName
	 * @param string $path
	 * @param array $tVars
	 * @return string
	 */
	public static function phpUser(GDO_User $user, $moduleName, $path,
	array $tVars = null)
	{
		$old = Trans::$ISO;
		Trans::setISO($user->getLangISO());
		$result = self::php($moduleName, $path, $tVars);
		Trans::$ISO = $old;
		return $result;
	}

	/**
	 * Render a template via include.
	 *
	 * @param string $moduleName
	 * @param string $path
	 * @param array $tVars
	 * @return string
	 */
	public static function php($moduleName, $path, array $tVars = null)
	{
		try
		{
			ob_start();
			self::$CALLS++;
			$path = self::getPath($moduleName, $path);
			if (GDO_GDT_DEBUG)
			{
				$message = $path;
				if (GDO_GDT_DEBUG >= 2)
				{
					$message = Debug::backtrace($message, false);
				}
				Logger::log('tpl', $message);
			}
			if ($tVars)
			{
				foreach ($tVars as $__key => $__value)
				{
					# make tVars locals for the template.
					$$__key = $__value;
				}
			}
			include $path; # a hell of a bug is to supress errors here.
			return ob_get_contents();
		}
		catch (\Throwable $ex)
		{
			Logger::logException($ex);
			return html(ob_get_contents()) .
				Debug::debugException($ex);
		}
		finally
		{
			ob_end_clean();
		}
	}

	/**
	 *
	 * @param string $moduleName
	 * @param string $path
	 * @param array $tVars
	 * @return self
	 */
	public static function templatePHP($moduleName, $path, array $tVars = null)
	{
		return self::make()->template($moduleName, $path, $tVars);
	}

	public static function responsePHP($moduleName, $path, array $tVars = null)
	{
		return GDT_Response::makeWith(
			self::templatePHP($moduleName, $path, $tVars));
	}

	/**
	 * Include a static file.
	 *
	 * @param string $moduleName
	 * @param string $path
	 * @return string
	 */
	public static function file($moduleName, $path)
	{
		self::$CALLS++;
		$path = self::getPath($moduleName, $path);
		return file_get_contents($path);
	}

	# ########################
	# ## Path substitution ###
	# ########################
	private static $PATHES = [];

	/**
	 * Get the Path for the GWF Design if the file exists
	 *
	 * @param string $path
	 *        templatepath
	 * @return string
	 */
	private static function getPath($moduleName, $path)
	{
		return self::getPathB($moduleName, $path);
		# Cache version is not that fast?
// 		$key = Trans::$ISO . $moduleName . $path;
// 		if ( !isset(self::$PATHES[$key]))
// 		{
// 			self::$PATHES[$key] = self::getPathB($moduleName, $path);
// 		}
// 		return self::$PATHES[$key];
	}

	private static function getPathB($moduleName, $path)
	{
		$isos = array_unique(
		[
			'_' . Trans::$ISO,
			'_' . GDO_LANGUAGE,
			'_en',
			'', # 
		]);

		# cut at dot.
		$path12 = Strings::rsubstrTo($path, '.', $path);
		$path13 = Strings::rsubstrFrom($path, '.', '');

		# Try themes first
		foreach (Application::instance()->getThemes() as $theme)
		{
			if (isset(self::$THEMES[$theme]))
			{
				foreach ($isos as $iso)
				{
					$path1 = $path12 . $iso . '.' . $path13;
					$path1 = self::$THEMES[$theme] . "/$moduleName/tpl/$path1";
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
		
		throw new GDOError('err_missing_template', [html("$moduleName/tpl/$path")]);
	}

}
