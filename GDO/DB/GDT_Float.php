<?php
namespace GDO\DB;

class GDT_Float extends GDT_Int
{
	public function toValue($var) { return $var === null ? null : (float) $var; }
	
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
	 * {@inheritDoc}
	 * @see \GDO\DB\GDT_Int::inputToVar()
	 */
	public function inputToVar($input)
	{
	    return self::inputToVarS($input);
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
	
	public static function displayS($var, $decimals=5, $dot=null, $comma=null)
	{
	    $dot = $dot !== null ? $dot : self::decimalPoint();
	    $comma = $comma != null ? $comma : self::thousandSeperator();
	    $display = number_format($var, $decimals, $dot, $comma);
	    return $display;
	}
	
	public $decimals = 5;
	public function decimals($decimals)
	{
	    $this->decimals = $decimals;
	    return $this;
	}
	
	public function renderCell()
	{
	    return self::displayS($this->var, $this->decimals);
	}
	
}
