<?php
namespace GDO\DB;
use GDO\Form\GDT_Select;
/**
 * Boolean Checkbox
 * @author gizmore
 * @since 5.0
 * @version 6.0
 */
class GDT_Checkbox extends GDT_Select
{
    public function __construct()
    {
        $this->emptyValue = '2';
        $this->min = $this->max = 1;
    }
    
    public function gdoColumnDefine()
    {
        return "{$this->identifier()} TINYINT UNSIGNED {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
    }
    
    public $undetermined;
    public function undetermined($undetermined)
    {
        $this->undetermined = $undetermined;
        return $this;
    }
   
    public function toVar($value)
    {
        if ($value === true) { return '1'; }
        elseif ($value === false) { return '0'; }
        else return null;
    }
    
    public function toValue($var)
    {
        if ($var === '0') { return false; }
        elseif ($var === '1') { return true; }
        else { return null; }
    }
    
    public function initChoices()
    {
        if (!$this->choices)
        {
            $this->choices = [];
            if ($this->undetermined)
            {
                $this->emptyInitial(t('please_choose'), $this->emptyValue);
            }
            $this->choices(array(
                '0' => t('no'),
                '1' => t('yes'),
            ));
        }
        return $this;
    }
    
    ################
    ### Validate ###
    ################
    public function validate($value)
    {
        $this->initChoices();
        return parent::validate($value);
    }
    
    public function renderForm()
    {
        $this->initChoices();
        return parent::renderForm();
    }
    
}
