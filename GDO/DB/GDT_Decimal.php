<?php
namespace GDO\DB;

use GDO\Core\GDT_Template;

/**
 * A fixed decimal, database driven field.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 6.1.0
 * 
 * @see GDT_Float
 * @see \GDO\Payment\GDT_Money
 */
class GDT_Decimal extends GDT_Int
{
	###########
	### GDT ###
	###########
	public function gdoColumnDefine()
	{
		$digits = sprintf("%d,%d", $this->digitsBefore + $this->digitsAfter, $this->digitsAfter);
		return "{$this->identifier()} DECIMAL($digits){$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	public function configJSON()
	{
		return array_merge(parent::configJSON(), [
			'digitsBefore' => $this->digitsBefore,
			'digitsAfter' => $this->digitsAfter,
		]);
	}
	
	##############
	### Digits ###
	##############
	public $digitsBefore = 5;
	public $digitsAfter = 5;
	
	public function digitsBefore($before)
	{
		return $this->digits($before, $this->digitsAfter);
	}
	
	public function digitsAfter($after)
	{
		return $this->digits($this->digitsBefore, $after);
	}
	
	public function digits($before, $after)
	{
		$this->digitsBefore = $before;
		$this->digitsAfter = $after;
		# compute step automatically nicely
		$step = $after < 1 ? 1 : floatval('0.'.str_repeat('0', $after-1).'1');
		return $after < 1 ? $this->step(1) : $this->step(sprintf("%.0{$after}f", $step));
	}
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    return GDT_Float::displayS($this->getVar(), $this->digitsAfter);
	}
	
	public function renderForm()
	{
		return GDT_Template::php('DB', 'form/decimal.php', ['field'=>$this]);
	}

	#############
	### Value ###
	#############
	public function _inputToVar($input)
	{
		if ($input = parent::_inputToVar($input))
		{
			return GDT_Float::inputToVarS($input);
		}
	}
	
	public function plugVar()
	{
		return "3.14";
	}
	
	public function toVar($value)
	{
		$var = $value === null ? null : sprintf("%.0{$this->digitsAfter}f", $value);
		return $var;
	}
	
	public function toValue($var)
	{
		return $var === null ? null : round($var, $this->digitsAfter);
	}
	
}
