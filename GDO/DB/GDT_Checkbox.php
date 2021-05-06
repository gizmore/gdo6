<?php
namespace GDO\DB;

use GDO\Form\GDT_Select;
use GDO\Core\GDT_Template;

/**
 * Boolean Checkbox.
 * Implemented as select to reflect undetermined status. Also HTML does not send unchecked boxes over HTTP.
 * 
 * @TODO what about real checkboxes? Not a single one wanted/needed?
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 5.0
 */
class GDT_Checkbox extends GDT_Select
{
    # db var representation. Null is the third state.
    const UNDETERMINED = '2';
    const TRUE = '1';
    const FALSE = '0';
    
	protected function __construct()
	{
	    parent::__construct();
		$this->emptyValue = '2';
		$this->min = 0;
		$this->max = 1;
		$this->ascii(); # This enables string search (not binary).
		$this->caseS();
		$this->notNull = true;
	}
	
	public function initChoices()
	{
		if ($this->choices === null)
		{
			$this->choices([
				'0' => t('enum_no'),
				'1' => t('enum_yes'),
			]);
			if ($this->undetermined)
			{
				$this->emptyInitial(t('please_choose'), $this->emptyValue);
				$this->choices[$this->emptyValue] = $this->displayEmptyLabel();
			}
		}
		return $this;
	}
	
	################
	### Database ###
	################
	/**
	 * Get TINYINT(1) column define.
	 * {@inheritDoc}
	 * @see \GDO\DB\GDT_String::gdoColumnDefine()
	 */
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} TINYINT(1) UNSIGNED {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	/**
	 * Return no collation for a tinyint.
	 * {@inheritDoc}
	 * @see \GDO\DB\GDT_String::gdoCollateDefine()
	 */
	public function gdoCollateDefine($caseSensitive)
	{
	    return '';
	}

	
	####################
	### Undetermined ###
	####################
	public $undetermined = false;
	public function undetermined($undetermined=true)
	{
		$this->undetermined = $undetermined;
		return $this;
	}
	
	###################
	### Var / Value ###
	###################
	public function toVar($value)
	{
		if ($value === true) { return '1'; }
		elseif ($value === false) { return '0'; }
		else { return null; }
	}
	
	public function toValue($var)
	{
		if ($var === '0') { return false; }
		elseif ($var === '1') { return true; }
		else { return null; }
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
		$this->initChoices();
		return parent::validate($value);
	}
	
	##############
	### Render ###
	##############
	public function displayValue($var)
	{
	    if ($var === null)
	    {
	        return t('enum_undetermined_yes_no');
	    }
	    switch ($var)
	    {
	        case '0': return t('enum_no');
	        case '1': return t('enum_yes');
	        default: return $this->errorInvalidVar($var);
	    }
	}
	
	protected function errorInvalidVar($var)
	{
	    return t('err_invalid_gdt_var', [$this->gdoHumanName(), html($var)]);
	}
	
	public function htmlClass()
	{
		return parent::htmlClass() . " gdt-checkbox-{$this->getVar()}";
	}
	
	public function renderForm()
	{
		$this->initChoices();
		$this->initThumbIcon();
		return parent::renderForm();
	}
	
	public function renderCell()
	{
	    return $this->displayValue($this->getVar());
	}
	
	public function renderFilter($f)
	{
		return GDT_Template::php('DB', 'filter/checkbox.php', ['field' => $this, 'f'=> $f]);
	}

	####################
	### Dynamic Icon ###
	####################
	/**
	 * Init label icon with thumb up or thumb down.
	 * @return \GDO\DB\GDT_Checkbox
	 */
	private function initThumbIcon()
	{
	    switch ($this->getVar())
	    {
	        case '0': return $this->icon('thumbs_down');
	        case '1': return $this->icon('thumbs_up');
	        default: return $this->icon('thumbs_none');
	    }
	}

}
