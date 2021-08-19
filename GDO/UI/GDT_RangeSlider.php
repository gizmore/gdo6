<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;
/**
 * Slider for range input with 2 handles.
 * In web1.0 themes, 2 inputs are used instead.
 * This GDT does not create a database column and is intended to be used in forms only.
 * @author gizmore
 * @version 6.05
 * @since 6.00
 * @see GDT_Slider
 */
final class GDT_RangeSlider extends GDT_Slider
{
	public function renderForm() { return GDT_Template::php('UI', 'form/range_slider.php', ['field' => $this]); }

	###########
	### GDO ###
	###########
	public function getGDOData() { return [$this->name => $this->getLow(), $this->highName => $this->getHigh()]; }

	###############
	### Options ###
	###############
	public $highName;
	public function highName($highName) { $this->highName = $highName; return $this; }
	public $minRange = -1;
	public function minRange($minRange) { $this->minRange = $minRange; return $this; }
	public $maxRange = -1;
	public function maxRange($maxRange) { $this->maxRange = $maxRange; return $this; }

	###################
	### Var / Value ###
	###################
	public function toVar($value) { return $value === null ? null : json_encode($value); }
	public function toValue($var) { return $var === null ? null : json_decode($var); }
	public function getLow() { return $this->getVal(0); }
	public function getHigh() { return $this->getVal(1); }
	private function getVal($i) { $v = $this->getValue(); return $v ? $v[$i] : $v; }
	public function initialLow() { return $this->var ? json_decode($this->var)[0] : null; }
	public function initialHigh() { return $this->var ? json_decode($this->var)[1] : null; }
	public function initialValue($value) { $this->initial = $this->var = $this->toVar($value); return parent::initialValue($value); }
	public function getValue()
	{
		if ($lo = $this->getRequestVar($this->formVariable(), $this->initial))
		{
			# 1 field json mode
			if ($lo[0] === '[')
			{
				return json_decode($lo);
			}
			# 2 field 1.0 mode
			elseif ($hi = $this->getRequestVar($this->formVariable(), null, $this->highName))
			{				
				return [$lo, $hi];
			}
		}
	}

	################
	### Validate ###
	################
	public function validate($value)
	{
		list($lo, $hi) = $value;
		if ( (parent::validate($lo)) && (parent::validate($hi)) )
		{
			$range = $hi - $lo;
			if ( ($this->minRange >= 0) && ($range < $this->minRange) )
			{
				return $this->error('err_range_underflow', [$this->minRange]);
			}
			if ( ($this->maxRange >= 0) && ($range > $this->maxRange) )
			{
				return $this->error('err_range_exceed', [$this->maxRange]);
			}
			return true;
		}
	}
}
