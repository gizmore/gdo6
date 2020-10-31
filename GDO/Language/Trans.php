<?php
namespace GDO\Language;

use GDO\Core\GDT_Error;
use GDO\Core\GDOError;

/**
 * Very cheap i18n.
 * 
 * @author gizmore
 * @since 1.00
 * @version 6.10
 */
final class Trans
{
	public static $ISO = 'en';
	
	private static $PATHS = [];
	private static $CACHE;
	private static $INITED = false;
	
	public static $NO_SITENAME = false;
	
	public static function setISO($iso)
	{
		# Set Trans locale
		self::$ISO = $iso;
		# Generate utf8 locale identifier, e.g: de_DE.utf8 and setlocale
		$iso = $iso . '_' . strtoupper($iso) . '.utf-8';
		if (!setlocale(LC_TIME, $iso))
		{
			setlocale(LC_TIME, $iso); # Bug... sometimes it needs two calls?!
		}
	}
	
	public static function numFiles()
	{
		return count(self::$PATHS);
	}
	
	public static function addPath($path)
	{
		self::$PATHS[] = $path;
	}
	
	public static function inited()
	{
		self::$INITED = true;
		self::$CACHE = [];
	}
	
	public static function getCache($iso)
	{
		return self::load($iso);
	}
	
	public static function load($iso)
	{
		if (!isset(self::$CACHE[$iso]))
		{
			self::reload($iso);
		}
		return self::$CACHE[$iso];
	}
	
	public static function t($key, array $args=null)
	{
		return self::tiso(self::$ISO, $key, $args);
	}
	
	public static function tiso($iso, $key, array $args=null)
	{
		$cache = self::load($iso);

		if ($text = @$cache[$key])
		{
			if ($args)
			{
				if (!($text = @vsprintf($text, $args)))
				{
					$text = $cache[$key] . ': ';
					$text .= json_encode($args);
				}
			}
		}
		else # Fallback key + printargs
		{
			$text = $key;
			if ($args)
			{
				$text .= ": ";
				$text .= json_encode($args);
			}
			
		}
		
		return $text;
// 		return self::$NO_SITENAME ? self::filterSitename($text) : $text;
	}

	private static function reload($iso)
	{
		$trans = [];
		$trans2 = [];
		if (self::$INITED)
		{
// 			if (false === ($loaded = Cache::get("gdo_trans_$iso")))
// 			{
				foreach (self::$PATHS as $path)
				{
				    $pathISO = "{$path}_{$iso}.php";
					if (is_file($pathISO))
					{
					    try
					    {
    						$trans2[] = include($pathISO);
					    }
					    catch (\Throwable $e)
					    {
					        self::$CACHE[$iso] = $trans;
					        echo GDT_Error::responseException($e)->renderCell();
					    }
					}
					else
					{
					    $pathEN= "{$path}_en.php";
						try
						{
						    $trans2[] = include($pathEN);
						}
						catch (\Throwable $e)
						{
						    self::$CACHE[$iso] = $trans;
						    echo GDT_Error::responseException($e)->renderCell();
						    throw new GDOError('err_langfile_corrupt', [$pathEN]);
						}
					}
				}
    			$trans = array_merge(...$trans2);
				$loaded = $trans;
//		 		Cache::set("gdo_trans_$iso", $loaded);
// 			}
			$trans = $loaded;
		}
		self::$CACHE[$iso] = $trans;
	}
	
	public static function hasKey($key)
	{
		return self::hasKeyIso(self::$ISO, $key);
	}
	
	public static function hasKeyIso($iso, $key)
	{
		$cache = self::load($iso);
		return !!@$cache[$key];
	}
}
