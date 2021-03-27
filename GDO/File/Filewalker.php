<?php
namespace GDO\File;
/**
 * Directory traversing utility.
 * @author gizmore
 * @version 6.10
 * @since 5.00
 */
final class Filewalker
{
	public static function filewalker_stub($entry, $fullpath, $args=null) {}
	
	public static function traverse($path, $pattern=null, $callback_file=false, $callback_dir=false, $recursive=100, $args=null)
	{
		if (is_array($path))
		{
			foreach ($path as $_path)
			{
				self::traverse($_path, $pattern, $callback_file, $callback_dir, $recursive, $args);
			}
			return;
		}
		
		$path = rtrim($path, '/\\');
		
		# Readable?
		if (!($dir = @dir($path)))
		{
			return false;
		}
		
		$dirstack = [];
		$filestack = [];
		while ($entry = $dir->read())
		{
			$fullpath = $path.'/'.$entry;
			if ( (strpos($entry, '.') === 0) ) # || (!is_readable($fullpath)) )
			{
				continue;
			}
			
			if (is_dir($fullpath))
			{
				$dirstack[] = array($entry, $fullpath);
			}
			elseif (FileUtil::isFile($fullpath))
			{
			    if ($pattern)
			    {
			        if (!preg_match($pattern, $entry))
			        {
			            continue;
			        }
			    }
			    $filestack[] = array($entry, $fullpath);
			}
		}
		$dir->close();
		
		usort($filestack, function($a, $b) {
			if (is_numeric($a[0]) && is_numeric($b[0]))
			{
				return $a[0] - $b[0];
			}
			return strcasecmp($a[0], $b[0]);
		});
		
		if (!is_bool($callback_file))
		{
    		foreach ($filestack as $file)
    		{
    			call_user_func($callback_file, $file[0], $file[1], $args);
    		}
		}
		
		usort($dirstack, function($a, $b){ return strcasecmp($a[0], $b[0]); });

	    if (!is_bool($callback_dir))
	    {
	        foreach ($dirstack as $d)
    		{
    			call_user_func($callback_dir, $d[0], $d[1], $args);
		    }
	    }
			
        if ($recursive)
		{
            foreach ($dirstack as $d)
            {
                self::traverse($d[1], $pattern, $callback_file, $callback_dir, $recursive-1, $args);
            }
		}
	}
	
}
