<?php
namespace GDO\File;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use GDO\Util\Strings;

/**
 * File system utilities.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.0
 */
final class FileUtil
{
    ##############
    ### Basics ###
    ##############
    public static function isFile($filename) { return stream_resolve_include_path($filename) !== false; } # fast check
    public static function isDir($filename) { return is_dir($filename); }
	public static function createDir($path) { return (self::isDir($path) && is_writable($path)) ? true : mkdir($path, GDO_CHMOD, true); }
	
	###############
	### Dirsize ###
	###############
	/**
	 * Get the size of a folder recursively.
	 * @param string $path
	 * @return int
	 */
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
	 * @deprecated should not matter.
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
		return array_slice(scandir($dir), 2);
	}
	
	/**
	 * Remove a dir recursively, file by file.
	 * @deprecated use rm -rf
	 * @param string $dir
	 * @return boolean
	 */
	public static function removeDir($dir)
	{
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
			{
				if ($object !== "." && $object !== "..")
				{
					if (is_dir($dir."/".$object))
					{
						self::removeDir($dir."/".$object);
					}
					else
					{
					    if (!unlink($dir."/".$object))
					    {
					        return false;
					    }
					}
				}
			}
			return rmdir($dir);
		}
		return true;
	}

	################
	### Filesize ###
	################
	/**
	 * Convert bytes to human filesize like "12.29kb".
	 * @example humanFilesize(12288, 1000, 3); # => 12.288kb
	 * @param int $bytes
	 * @param int $factor - 1024 or 1000 should be used
	 * @param int $digits - number of fraction digits
	 * @return string
	 */
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
	
	/**
	 * Converts a human filesize to bytes as integer.
	 * @example humanToBytes("12kb"); # => 12288
	 * @param string $s
	 * @return int
	 */
	public static function humanToBytes($s)
	{
	    $txt = t('_filesize');
	    foreach ($txt as $pow => $b)
	    {
	        if ($pow > 0)
	        {
	            if (stripos($s, $b) !== false)
	            {
	                $mul = preg_replace('/[^\\.0-9]/', '', $s);
	                return (int) bcmul($mul, bcpow(1024, $pow));
	            }
	        }
	    }
	    return (int) $s;
	}
	
	#########################
	### Merge Directories ###
	#########################
	/**
	 * Merge two directories recursively.
	 * @TODO reorder params as $source, $dest
	 * @param string $target
	 * @param string $source
	 */
	public static function mergeDirectory($target, $source)
	{
	    Filewalker::traverse($source, null, function($entry, $fullpath) use ($source, $target) {
	        $newpath = str_replace($source, $target, $fullpath);
	        FileUtil::createDir(Strings::rsubstrTo($newpath, '/'));
	        copy($fullpath, $newpath);
	    });
	}
	
	############
	### MIME ###
	############
	public static function mimetype($path)
	{
	    return mime_content_type($path);
	}
	
	##############
	### Sanity ###
	##############
	public static function saneFilename($filename)
	{
	    return str_replace(['/', '\\', '$', ':'], '#', $filename);
	}
	
}
