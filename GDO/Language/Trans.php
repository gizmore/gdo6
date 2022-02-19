<?php
namespace GDO\Language;

use GDO\Core\GDT_Error;
use GDO\Core\GDOError;
use GDO\File\FileUtil;
use GDO\DB\Cache;

/**
 * Very cheap i18n.
 * Look at bottom for API.
 * 
 * @TODO: Check if ini file parsing and using would be faster than php include.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 1.0.0
 */
final class Trans
{
    /**
     * @var string
     */
	public static $ISO = 'en';
	
	public static $FILE_CACHE = GDO_FILECACHE;
	
	private static $HAS_LOADED_FILE_CACHE = false;

	/**
	 * Base pathes for translation data files.
	 * @var string[]
	 */
	private static $PATHS = [];
	
	/**
	 * Translation data cache.
	 * @var string[string]
	 */
	private static $CACHE = [];
	
	/**
	 * Are all pathes added?
	 * @var boolean
	 */
	private static $INITED = false;
	
	/**
	 * Shall sitename be appended to seo titles?
	 * @TODO move
	 * @var boolean
	 */
	public static $NO_SITENAME = false;
	
	/**
	 * Number of missing translation keys for stats and testing.
	 * @var integer
	 */
	public static $MISS = 0;
	
	/**
	 * The keys that are missing in translation.
	 * @var string[]
	 */
	public static $MISSING = [];
	
	/**
	 * Set the current ISO
	 * @param string $iso
	 */
	public static function setISO($iso)
	{
	    if ($iso !== self::$ISO)
	    {
    		# Set Trans locale
    		self::$ISO = $iso;
    		# Generate utf8 locale identifier, e.g: de_DE.utf8 and setlocale
    		$iso = $iso . '_' . strtoupper($iso) . '.utf-8';
//     		if (!setlocale(LC_TIME, $iso))
    		{
    			setlocale(LC_TIME, $iso); # Bug... sometimes it needs two calls?!
    		}
//     		self::inited(true);
	    }
	}
	
	/**
	 * Show number of registered translation data base pathes.
	 * @return int
	 */
	public static function numFiles()
	{
	    if (self::$HAS_LOADED_FILE_CACHE)
	    {
	        return 1;
	    }
		return count(self::$PATHS);
	}

	/**
	 * Add a translation file to the language file pathes.
	 * @param string $path
	 */
	public static function addPath($path)
	{
	    self::$PATHS[$path] = $path;
	}
	
	/**
	 * Set inited and clear cache.
	 * @TODO separate calls. maybe cache should not be cleared quickly? no idea. Make performance tests for language loading on init.
	 * @param bool $inited
	 */
	public static function inited($inited)
	{
		self::$INITED = $inited;
	    self::$CACHE = [];
	}
	
	/**
	 * Get the cache for an ISO.
	 * @param string $iso
	 * @return string[string]
	 */
	public static function getCache($iso)
	{
		return self::load($iso);
	}
	
	/**
	 * Load a translation data into and from cache.
	 * @param string $iso
	 * @return string[string]
	 */
	public static function &load($iso)
	{
		if (!isset(self::$CACHE[$iso]))
		{
			return self::reload($iso);
		}
		return self::$CACHE[$iso];
	}
	
	/**
	 * Translate into current ISO.
	 * @param string $key
	 * @param array $args
	 * @return string
	 */
	public static function t($key, array $args=null)
	{
		return self::tiso(self::$ISO, $key, $args);
	}
	
	/**
	 * Translate into an language ISO.
	 * @param string $iso
	 * @param string $key
	 * @param array $args
	 * @return string
	 */
	public static function tiso($iso, $key, array $args=null)
	{
	    if (!$key)
	    {
	        return '';
	    }
	    
		$cache = self::load($iso);

		if (isset($cache[$key]))
		{
		    $text = $cache[$key];
			if ($args)
			{
				if (!($text = @vsprintf($text, $args)))
				{
				    self::$MISS++;
				    self::$MISSING[] = $key;
					$text = $cache[$key] . ': ';
					$text .= json_encode($args);
				}
			}
		}
		else # Fallback key + printargs
		{
		    self::$MISS++;
		    self::$MISSING[] = $key;
		    $text = $key;
			if ($args)
			{
				$text .= ": ";
				$text .= json_encode($args);
			}
		}
		return $text;
	}

	private static function getCacheKey($iso)
	{
		$key = md5("$iso;" . implode(',', self::$PATHS));
		return $key;
	}
	
	private static function &reload($iso)
	{
		$trans = [];
		$trans2 = [];
		
		# Try cache
		$key = self::getCacheKey($iso);
		if (self::$FILE_CACHE && Cache::fileHas($key))
		{
		    $content = Cache::fileGetSerialized($key);
		    self::$CACHE[$iso] = $content;
		    self::$HAS_LOADED_FILE_CACHE = true;
		    return self::$CACHE[$iso];
		}
		
		# Build lang map
		if (self::$INITED)
		{
			foreach (self::$PATHS as $path)
			{
			    $pathISO = "{$path}_{$iso}.php";
				if (FileUtil::isFile($pathISO))
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
						if ($t2 = @include($pathEN))
						{
							$trans2[] = $t2;
						}
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
			$trans = $loaded;
    		self::$CACHE[$iso] = $trans;
    		
    		# Save cache
    		if (self::$FILE_CACHE)
    		{
    		    FileUtil::createDir(Cache::filePath());
    		    Cache::fileSetSerialized($key, $trans);
    		}
		}
		
		return $trans;
	}
	
	/**
	 * Check if a translation key exists.
	 * @param string $key
	 * @return boolean
	 */
	public static function hasKey($key, $withMiss=false)
	{
	    $result = self::hasKeyIso(self::$ISO, $key);
	    if ($withMiss && (!$result))
	    {
	        self::$MISS++;
	        self::$MISSING[] = $key;
	    }
	    return $result;
	}

	/**
	 * Check if a translation key exists for an ISO.
	 * @param string $iso
	 * @param string $key
	 * @return boolean
	 */
	public static function hasKeyIso($iso, $key)
	{
		$cache = self::load($iso);
		return isset($cache[$key]);
	}

}
