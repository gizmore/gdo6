<?php
namespace GDO\DB;

use GDO\Core\GDT_Template;

/**
 * A fixed decimal, database driven field.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 * 
 * @see GDT_Float
 */
class GDT_Decimal extends GDT_Int
{
	public $digitsBefore = 5;
	public $digitsAfter = 5;
	
	public function digits($before, $after)
	{
		$this->digitsBefore = $before;
		$this->digitsAfter = $after;
		$step = $after < 1 ? 1 : floatval('0.'.str_repeat('0', $after-1).'1');
		return $after < 1 ? $this->step(1) : $this->step(sprintf("%.0{$after}f", $step));
	}
	
	public function gdoColumnDefine()
	{
		$digits = sprintf("%d,%d", $this->digitsBefore + $this->digitsAfter, $this->digitsAfter);
		return "{$this->identifier()} DECIMAL($digits){$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	public function renderForm()
	{
		return GDT_Template::php('DB', 'form/decimal.php', ['field'=>$this]);
	}
	
	public function toValue($var)
	{
		return round($var, $this->digitsAfter);
	}
	
	public function configJSON()
	{
		return array_merge(parent::configJSON(), array(
			'digitsBefore' => $this->digitsBefore,
			'digitsAfter' => $this->digitsAfter,
		));
	}
}
