<?php
namespace GDO\Util;

/**
 * CSV Utilities
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class CSV
{
    private $path;
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    public function eachLine($callable)
    {
        $fh = fopen($this->path, 'r');
        while ($row = fgetcsv($fh))
        {
            $callable($row);
        }
    }
    
}
