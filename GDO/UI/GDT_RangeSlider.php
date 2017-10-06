<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;
/**
 * Slider for range input with 2 handles.
 * @author gizmore
 * @version 6.0
 * @since 6.0
 * @see GDT_Slider
 */
final class GDT_RangeSlider extends GDT_Slider
{
    public function renderForm() { return GDT_Template::php('UI', 'form/range_slider.php', ['field' => $this]); }
    
    #############
    ### Value ###
    #############
    public function toVar($value) { return $value === null ? null : json_encode($value); }
    public function toValue($var) { return $var === null ? null : json_decode($var); }

    public function getLow() { return $this->getVal(0); }
    public function getHigh() { return $this->getVal(1); }
    private function getVal($i) { $v = $this->getValue(); return $v ? $v[$i] : $v; }

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
    
    ################
    ### Validate ###
    ################
    public function validate($value)
    {
        $lo = $this->getLow(); $hi = $this->getHigh();
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
