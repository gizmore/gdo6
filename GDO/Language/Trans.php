<?php
namespace GDO\Language;
/**
 * Very cheap i18n.
 * 
 * @author gizmore
 * @since 1.00
 * @version 6.08
 */
final class Trans
{
	public static $ISO = 'en';
	
	private static $PATHS = [];
	private static $CACHE;
	private static $INITED = false;
	
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
	}

	private static function reload($iso)
	{
		$trans = [];
		if (self::$INITED)
		{
// 			if (false === ($loaded = Cache::get("gdo_trans_$iso")))
			{
				foreach (self::$PATHS as $path)
				{
					if (is_readable("{$path}_{$iso}.php"))
					{
						$trans2 = include("{$path}_{$iso}.php");
					}
					else
					{
						$trans2 = require("{$path}_en.php");
					}
					$trans = array_merge($trans, $trans2);
				}
				$loaded = $trans;
//		 		Cache::set("gdo_trans_$iso", $loaded);
			}
			$trans = $loaded;
		}
		self::$CACHE[$iso] = $trans;
	}
}
