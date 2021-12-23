<?php
namespace GDO\DB;

use GDO\Core\GDO;

/**
 * Floating points return a float scalar as value.
 * @author gizmore
 */
class GDT_Float extends GDT_Int
{
	public function toValue($var)
	{
		return ( ($var === null) ||
			     (trim($var, "\r\n\t ") ==='') ) ?
			null : (float) $var;
	}
	
	public function gdoColumnDefine()
	{
		$unsigned = $this->unsigned ? " UNSIGNED" : "";
		return "{$this->identifier()} FLOAT{$unsigned}{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
		
	public function htmlClass()
	{
		return sprintf(' gdt-float %s', parent::htmlClass());
	}

	/**
	 * Replace , with . for user input.
	 */
	public function _inputToVar($input)
	{
		if (parent::_inputToVar($input))
		{
		    return self::inputToVarS($input);
		}
	}
	
	public static function inputToVarS($input)
	{
	    return str_replace(',', '.', $input);
	}
	
	public static function thousandSeperator()
	{
	    return t('thousands_seperator');
	}
	
	public static function decimalPoint()
	{
	    return t('decimal_point');
	}
	
	public static function displayS($var, $decimals=4, $dot=null, $comma=null)
	{
		if ($var !== null)
		{
		    $dot = $dot !== null ? $dot : self::decimalPoint();
		    $comma = $comma != null ? $comma : self::thousandSeperator();
		    $display = number_format($var, $decimals, $dot, $comma);
		    return $display;
		}
	}
	
	public $decimals = 4;
	public function decimals($decimals)
	{
	    $this->decimals = $decimals;
	    return $this;
	}
	
	public function renderCell()
	{
	    return self::displayS($this->var, $this->decimals);
	}
	
	public function gdoCompare(GDO $a, GDO $b)
	{
		$va = $a->getValue($this->name);
		$vb = $b->getValue($this->name);
		if ($va > $vb) { return 1; }
		if ($vb > $va) { return -1; }
		return 0;
	}
	
}
