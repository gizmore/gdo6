<?php
namespace GDO\File;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class FileUtil
{
    public static function isFile($filename) { return is_file($filename) && is_readable($filename); }
    public static function isDir($filename) { return is_dir($filename) && is_readable($filename); }
    public static function createDir($path) { return self::isDir($path) && is_writable($path) ? true : @mkdir($path, GWF_CHMOD, true); }
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
            rmdir($dir);
        }
    }
    
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
    
}
