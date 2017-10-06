<?php
namespace GDO\DB;
use GDO\Util\Random;
/**
 * Default random token is 16 chars alphanumeric.
 * 
 * @author gizmore
 * @since 4.0
 * @version 6.0
 */
class GDT_Token extends GDT_Char
{
    public static $LENGTH = 16;
    
    public function defaultLabel() { return $this->label('token'); }
    
    public function __construct()
    {
        $this->size(self::$LENGTH);
    }
    
    public function size($size)
    {
        $this->pattern = '/^[a-zA-Z0-9]{'.$size.'}$/d';
        return parent::size($size);
    }
    
    public $initialNull = false;
    public function initialNull($initialNull=true)
    {
        $this->initialNull = $initialNull;
        return $this;
    }
    
    public function blankData()
    {
        return [$this->name => $this->initialNull?null:Random::randomKey($this->max)];
    }
    
}
