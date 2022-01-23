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
    private $withHeader = true;
    
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
    
    public function withHeader($withHeader=true)
    {
        $this->withHeader = $withHeader;
        return $this;
    }
    
    public function eachLine($callable)
    {
        if ($fh = @fopen($this->path, 'r'))
        {
            $first = $this->withHeader;
            while ($row = fgetcsv($fh, null, $this->delimiter, $this->enclosure))
            {
                if ($first)
                {
                    $first = false;
                }
                else
                {
                    $callable($row);
                }
            }
            fclose($fh);
        }
    }
    
    public function all()
    {
        $all = [];
        $fh = fopen($this->path, 'r');
        $first = $this->withHeader;
        while ($row = fgetcsv($fh, null, $this->delimiter, $this->enclosure, "\""))
        {
            if ($first)
            {
                $first = false;
            }
            else
            {
                $all[] = $row;
            }
        }
        return $all;
    }
    
}
