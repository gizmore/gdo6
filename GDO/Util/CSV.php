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
    private $delimiter = ',';
    private $enclosure = '"';
    
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    public function delimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }
    
    public function enclosure($enclosure)
    {
        $this->enclosure = $enclosure;
        return $this;
    }
    
    public function eachLine($callable)
    {
        $fh = fopen($this->path, 'r');
        while ($row = fgetcsv($fh, null, $this->delimiter, $this->enclosure))
        {
            $callable($row);
        }
    }
    
}
