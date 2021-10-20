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
 * @version 6.10.4
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
	public static function createFile($path)
	{
	    if (!self::isFile($path))
	    {
	        if (!@touch($path))
	        {
	            return false;
	        }
	    }
	    return true;
	}
	
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
				if ($object !== '.' && $object !== '..')
				{
				    $obj = "{$dir}/{$object}";
					if (is_dir($obj))
					{
						self::removeDir($obj);
					}
					else
					{
					    if (!unlink($obj))
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
		$txt = self::getTextArray();
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
	
	private static function getTextArray()
	{
	    $txt = t('_filesize');
	    if (!is_array($txt))
	    {
	        $txt = [
	            'B',
	            'KB',
	            'MB',
	            'GB',
	            'TB',
	            'PB',
	        ];
	    }
	    return $txt;
	}
	
	/**
	 * Converts a human filesize to bytes as integer.
	 * @example humanToBytes("12kb"); # => 12288
	 * @param string $s
	 * @return int
	 */
	public static function humanToBytes($s)
	{
	    $txt = self::getTextArray();
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
        $suffix = substr($path, -3);
        switch($suffix)
        {
            case '.js': return 'text/javascript';
            case 'css': return 'text/css';
            case 'php': return 'text/x-php';
        }
	    return mime_content_type($path);
	}
	
	##############
	### Sanity ###
	##############
	public static function saneFilename($filename)
	{
	    return str_replace(['/', '\\', '$', ':'], '#', $filename);
	}
	
	################
	### LastLine ###
	################
	/**
	 * Get the last line of a file.
	 * @param string $filename
	 * @throws \Throwable
	 * @return string
	 */
	public static function lastLine($filename)
	{
	    try
	    {
    	    $fh = fopen($filename, "r");
            return self::_lastLine($fh);
	    }
	    catch (\Throwable $ex)
	    {
	        throw $ex;
	    }
	    finally
	    {
	        if ($fh)
	        {
	            @fclose($fh);
	        }
	    }
	}
	
	/**
	 * Get the last line from a filehandle.
	 * Destroys seek.
	 * @param resource $fh
	 * @return string
	 */
	public static function _lastLine($fh)
	{
	    $line = '';

	    $cursor = -1;
	    fseek($fh, $cursor, SEEK_END);
	    $char = fgetc($fh);
	    
	    /**
	     * Trim trailing newline chars of the file
	     */
	    while ($char === "\n" || $char === "\r")
	    {
	        fseek($fh, $cursor--, SEEK_END);
	        $char = fgetc($fh);
	    }
	    
	    /**
	     * Read until the start of file or first newline char
	     */
	    while ($char !== false && $char !== "\n" && $char !== "\r")
	    {
	        /**
	         * Prepend the new char
	         */
	        $line = $char . $line;
	        fseek($fh, $cursor--, SEEK_END);
	        $char = fgetc($fh);
	    }
	    
	    return $line;
	}
	
}
