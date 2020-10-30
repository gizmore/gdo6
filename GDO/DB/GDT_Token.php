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
		$this->length(self::$LENGTH);
	}
	
	public function length($size)
	{
		$this->pattern = '/^[a-zA-Z0-9]{'.$size.'}$/D';
		return parent::length($size);
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
