<?php
namespace GDO\DB;
use GDO\Core\GDT_Template;
class GDT_Decimal extends GDT_Int
{
	public $digitsBefore = 5;
	public $digitsAfter = 5;
	
	public function digits($before, $after)
	{
		$this->digitsBefore = $before;
		$this->digitsAfter = $after;
		$step = $after < 1 ? 1 : floatval('0.'.str_repeat('0', $after-1).'1');
		return $this->step($step);
	}
	
	public function gdoColumnDefine()
	{
		$digits = sprintf("%d,%d", $this->digitsBefore + $this->digitsAfter, $this->digitsAfter);
		return "{$this->identifier()} DECIMAL($digits){$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	public function render()
	{
		return GDT_Template::php('Type', 'form/decimal.php', ['field'=>$this]);
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
