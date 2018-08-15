<?php
namespace GDO\Form;

use GDO\Core\GDT_Template;
use GDO\Core\GDO;

class GDT_Select extends GDT_ComboBox
{
	const SELECTED = ' selected="selected"';
	
	public function getVar()
	{
		if (null === ($value = $this->getRequestVar('form', $this->var)))
		{
			$value = $this->multiple ? '[]' : null;
		}
		elseif ($this->multiple)
		{
			if (is_array($value))
			{
				$value = json_encode($value);
			}
		}
		elseif ($value === $this->emptyValue)
		{
			$value = null;
		}
		return $value;
	}
	
	public function getValue()
	{
		if (null === ($var = $this->getVar()))
		{
			return $this->multiple ? [] : $this->emptyValue;
		}
		return $this->toValue($var);
	}

	public function toVar($value)
	{
		if ($this->multiple)
		{
			return json_encode(array_values($value));
		}
		else 
		{
			return $value === $this->emptyValue ? null : $value;
		}
	}

	public function toValue($var)
	{
		return $this->multiple ? json_decode($var) : ($var === $this->emptyValue ? null : $var);
	}
	
	public function getGDOData()
	{
		return [$this->name => ($this->var === $this->emptyValue ? null : $this->var)];
	}
	
	public function setGDOData(GDO $gdo=null)
	{
		return $gdo ? parent::setGDOData($gdo) : $this->val($this->emptyValue);
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
		return $this->multiple ? $this->validateMultiple($value) : $this->validateSingle($value);
	}
	
	private function validateMultiple($values)
	{
		foreach ($values as $value)
		{
			if (!$this->validateSingle($value))
			{
				return false;
			}
		}
		
		if ( ($this->minSelected !== null) && ($this->minSelected > count($values)) )
		{
			return $this->error('err_select_min', [$this->minSelected]);
		}
		
		if ( ($this->maxSelected !== null) && ($this->maxSelected < count($values)) )
		{
			return $this->error('err_select_max', [$this->maxSelected]);
		}
		
		return true;
	}
	
	protected function validateSingle($value)
	{
		if ( ($value === null) || ($value === $this->emptyValue) )
		{
			return $this->isRequired() ? $this->errorNotNull() : true;
		}
		
		if (!isset($this->choices[$value]))
		{
			return $this->error('err_invalid_choice');
		}
		return true;
	}
	
	###############
	### Choices ###
	###############
	public $emptyValue = '0';
	public function emptyValue($emptyValue)
	{
		$this->emptyValue = $emptyValue;
		return $this->emptyLabel(t('please_choice'));
	}
	public $emptyLabel;
	public function emptyLabel($emptyLabel)
	{
		$this->emptyLabel = $emptyLabel;
		return $this;
	}
	public function emptyInitial($emptyLabel, $emptyValue='0')
	{
		$this->emptyValue = $emptyValue;
		$this->emptyLabel = $emptyLabel;
		return $this->initial($emptyValue);
	}
	
	public function htmlSelected($value)
	{
		if ($this->multiple)
		{
			return in_array($value, $this->getValue()) ? self::SELECTED : '';
		}
		else 
		{
			return $this->getVar() === (string)$value ? self::SELECTED : '';
		}
	}
	
	################
	### Multiple ###
	################
	public $multiple = false;
	public function multiple($multiple=true) { $this->multiple = $multiple; return $this; }
	
	public $minSelected;
	public $maxSelected;
	public function minSelected($minSelected)
	{
		$this->minSelected = $minSelected;
		return $this;
	}
	
	public function maxSelected($maxSelected)
	{
		$this->maxSelected = $maxSelected;
		return $this->multiple($maxSelected > 1);
	}
	
	##############
	### Render ###
	##############
	public function renderForm()
	{
		return GDT_Template::php('Form', 'form/select.php', ['field' => $this]);
	}
	
	public function renderJSON()
	{
		return array(
			'name' => $this->name,
			'multiple' => $this->multiple,
			'minSelected' => $this->minSelected,
			'maxSelected' => $this->maxSelected,
			'selected' => $this->multiple ? $this->getValue() : $this->getSelectedVar(),
			'error' => $this->error,
		);
	}
	
	public function getSelectedVar()
	{
		$var = $this->getVar();
		return $var === null ? $this->emptyValue : $var;
	}
}
