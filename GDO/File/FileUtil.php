<?php
namespace GDO\File;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use GDO\TBS\Module_TBS;
use GDO\Util\Strings;

/**
 * File system utilities.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
final class FileUtil
{
    public static function isFile($filename) { return stream_resolve_include_path($filename) !== false; } # fast check
    public static function isDir($filename) { return is_dir($filename); }
	public static function createDir($path) { return self::isDir($path) && is_writable($path) ? true : mkdir($path, GWF_CHMOD, true); }
	public static function dirsize($path)
	{
		$bytes = 0;
		$path = realpath($path);
		if (self::isDir($path))
		{
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file)
			{
				$bytes += $file->getSize();
			}
		}
		return $bytes;
	}
	
	/**
	 * Replace DIR separator with OS agnostic character.
	 * @param string $path
	 * @return string
	 */
	public static function path($path)
	{
	    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
	}
	
	/**
	 * Convert dir separator to unix style.
	 * Used in mysql source filename :/
	 * @param string $path
	 * @return string
	 */
	public static function linuxPath($path)
	{
	    return str_replace(['/', '\\'], '/', $path);
	}
	
/**
	 * Scandir without '.' and '..'. 
	 * @param string $dir
	 * @return array
	 */
	public static function scandir($dir)
	{
		$files = array_slice(scandir($dir), 2);
		return $files;
	}
	
	public static function removeDir($dir)
	{
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
			{
				if ($object != "." && $object != "..")
				{
					if (is_dir($dir."/".$object))
					{
						self::removeDir($dir."/".$object);
					}
					else
					{
						unlink($dir."/".$object);
					}
				}
			}
			return rmdir($dir);
		}
		return false;
	}

	################
	### Filesize ###
	################
	public static function humanFilesize($bytes, $factor='1024', $digits='2')
	{
		$txt = t('_filesize');
		$i = 0;
		$rem = '0';
		while (bccomp($bytes, $factor) >= 0)
		{
			$rem = bcmod($bytes, $factor);
			$bytes = bcdiv($bytes, $factor);
			$i++;
		}
		return $i === 0
		? sprintf("%s%s", $bytes, $txt[$i])
		: sprintf("%.0{$digits}f%s", ($bytes+$rem/$factor), $txt[$i]);
	}
	
	public static function humanToBytes($s)
	{
	    $txt = t('_filesize');
	    foreach ($txt as $pow => $b)
	    {
	        if ($pow > 0)
	        {
	            if (stripos($s, $b) !== false)
	            {
// 	                $pow += 1;
	                $mul = preg_replace('/[^\\.0-9]/', '', $mul);
	                return bcmul($mul, bcpow(1024, $pow));
	            }
	        }
	    }
	    return (int) $s;
	}
	
	#########################
	### Merge Directories ###
	#########################
	public static function mergeDirectory($target, $source)
	{
	    Filewalker::traverse($source, null, function($entry, $fullpath) use ($source, $target) {
	        $newpath = str_replace($source, $target, $fullpath);
	        FileUtil::createDir(Strings::rsubstrTo($newpath, '/'));
	        copy($fullpath, $newpath);
	    });
	}
	
}
