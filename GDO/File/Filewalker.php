<?php
namespace GDO\File;
/**
 * Directory traversing utility.
 * @author gizmore
 */
final class Filewalker
{
	public static function filewalker_stub($entry, $fullpath, $args=null) {}
	
	public static function traverse($path, $callback_file=false, $callback_dir=false, $recursive=true, $args=null)
	{
		$path = rtrim($path, '/\\');
		
		# Readable?
		if (!($dir = @dir($path)))
		{
			return false;
		}
		
		if (is_bool($callback_file))
		{
			$callback_file = array(__CLASS__, 'filewalker_stub');
		}
		
		if (is_bool($callback_dir))
		{
			$callback_dir = array(__CLASS__, 'filewalker_stub');
		}
		
		$dirstack = [];
		$filestack = [];
		while ($entry = $dir->read())
		{
			$fullpath = $path.'/'.$entry;
			if ( (strpos($entry, '.') === 0) || (!is_readable($fullpath)) )
			{
				continue;
			}
			
			if (is_dir($fullpath))
			{
				$dirstack[] = array($entry, $fullpath);
			}
			elseif (is_file($fullpath))
			{
				$filestack[] = array($entry, $fullpath);
			}
		}
		$dir->close();
		
		usort($filestack, function($a, $b){ return strcasecmp($a[0], $b[0]); });
		foreach ($filestack as $file)
		{
			call_user_func($callback_file, $file[0], $file[1], $args);
		}
		
		
		usort($dirstack, function($a, $b){ return strcasecmp($a[0], $b[0]); });
		foreach ($dirstack as $d)
		{
			call_user_func($callback_dir, $d[0], $d[1], $args);
			
			if ($recursive)
			{
				self::traverse($d[1], $callback_file, $callback_dir, $recursive, $args);
			}
		}
	}
}
