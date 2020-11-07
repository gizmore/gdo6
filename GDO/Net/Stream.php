<?php
namespace GDO\Net;
use GDO\File\GDO_File;
/**
 * File utility to stream downloads in chunks.
 * 
 * @author gizmore
 * @since 3.00
 * @version 6.07
 */
final class Stream
{
	public static function path($path)
	{
		$out = false;
		if (ob_get_level()>0)
		{
			$out = ob_end_clean();
		}
		$result = self::_path($path);
		if ($out !== false)
		{
			ob_start();
			echo $out;
		}
		return $result;
	}
	
	public static function _path($path)
	{
		if ($fh = fopen($path, 'rb'))
		{
			while (!feof($fh))
			{
				echo fread($fh, 1024*1024);
				flush();
			}
			fclose($fh);
			return true;
		}
		return false;
	}
	
	public static function file(GDO_File $file, $variant='')
	{
		self::path($file->getVariantPath($variant));
	}
	
	public static function serve(GDO_File $file, $variant='', $disposition=true)
	{
		header('Content-Type: '.$file->getType());
		header('Content-Size: '.$file->getSize());
		if ($disposition)
		{
			header('Content-Disposition: attachment; filename="'.htmlspecialchars($file->getName()).'"');
		}
		self::file($file, $variant);
		die();
	}
	
	/**
	 * Serve a HTTP range request if desired.
	 * @param GDO_File $gdoFile
	 * @param string $variant
	 */
	public static function serveWithRange(GDO_File $gdoFile, $variant='')
	{
	    $size = $gdoFile->getSize();
	    $start = 0;
	    $end = $size - 1;

	    $file = $gdoFile->getVariantPath($variant);
	    $fp = fopen($file, 'rb');
	    
	    header('Content-type: ' . $gdoFile->getType());
	    header('Accept-Ranges: 0-' . $size);

	    if (isset($_SERVER['HTTP_RANGE']))
	    {
            $c_start = $start;
	        $c_end = $end;
	        
	        
	        list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
	        if (strpos($range, ',') !== false)
	        {
	            header('HTTP/1.1 416 Requested Range Not Satisfiable');
	            header("Content-Range: bytes $start-$end/$size");
	            exit;
	        }
	        if ($range == '-')
	        {
	            $c_start = $size - substr($range, 1);
	        }
	        else
	        {
	            $range  = explode('-', $range);
	            $c_start = $range[0];
	            $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
	        }
	        $c_end = ($c_end > $end) ? $end : $c_end;
	        if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size)
	        {
	            header('HTTP/1.1 416 Requested Range Not Satisfiable');
	            header("Content-Range: bytes $start-$end/$size");
	            exit;
	        }
	        $start  = $c_start;
	        $end    = $c_end;
	        $length = $end - $start + 1;
	        fseek($fp, $start);
	        header('HTTP/1.1 206 Partial Content');
	    }
	    
	    header("Content-Range: bytes $start-$end/$size");
	    header("Content-Length: ".$length);
	    
	    $buffer = 1024 * 8;
	    while (!feof($fp) && ($p = ftell($fp)) <= $end)
	    {
	        if ($p + $buffer > $end)
	        {
	            $buffer = $end - $p + 1;
	        }
	        echo fread($fp, $buffer);
	        flush();
	    }
	    fclose($fp);
	}

}
