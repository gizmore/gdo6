<?php
namespace GDO\DB;
use GDO\Form\GDT_Select;
use GDO\Core\GDT_Template;
/**
 * Boolean Checkbox.
 * Implemented as select to reflect undetermined status. Also HTML does not send unchecked boxes.
 * @author gizmore
 * @since 5.0
 * @version 6.10
 */
class GDT_Checkbox extends GDT_Select
{
	public function __construct()
	{
		$this->emptyValue = '2';
		$this->min = 0;
		$this->max = 1;
		$this->notNull = true;
	}
	
	public function initChoices()
	{
		if ($this->choices === null)
		{
			$this->choices(array(
				'0' => t('enum_no'),
				'1' => t('enum_yes'),
			));
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
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} TINYINT(1) UNSIGNED {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
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
		else return null;
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
		switch ($this->getVar())
		{
			case '0': return t('enum_no');
			case '1': return t('enum_yes');
			default: return t('enum_undetermined_yes_no');
		}
	}
	
	public function renderFilter()
	{
		return GDT_Template::php('DB', 'filter/boolean.php', ['field'=>$this]);
	}

	####################
	### Dynamic Icon ###
	####################
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
