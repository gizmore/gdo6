<?php
namespace GDO\Form;

use GDO\Core\GDO;
use GDO\Core\GDT_Template;

class GDT_Select extends GDT_ComboBox
{
	const SELECTED = ' selected="selected"';
	
	public function inputToVar($input) { return $input; }
	
	public function getVar()
	{
// 		$this->fixEmptyMultiple();
	    $var = null;
		if (null === ($var = $this->getRequestVar($this->formVariable(), $this->var)))
		{
			$var = $this->multiple ? '[]' : null;
		}
		elseif ($this->multiple)
		{
			if (is_array($var))
			{
				$var = json_encode($var);
			}
		}
		elseif ($var === $this->emptyValue)
		{
			$var = null;
		}
		return $var;
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
		    if ($value)
		    {
		        return json_encode(array_values($value));
		    }
		    else
		    {
		        return null;
		    }
		}
		else 
		{
			return $value === $this->emptyValue ? null : $value;
		}
	}

	public function toValue($var)
	{
	    if ($this->multiple)
	    {
	        return json_decode($var);
	    }
	    if ($var === $this->emptyValue)
	    {
	        return null;
	    }
	    if (isset($this->choices[$var]))
	    {
	        return $this->choices[$var];
	    }
	    return $var;
	}
	
	public function getGDOData()
	{
		return [$this->name => ($this->var === $this->emptyValue ? null : $this->var)];
	}
	
	public function setGDOData(GDO $gdo=null)
	{
	    return (!$gdo) || $gdo->isTable() ? $this->var($this->emptyValue) : parent::setGDOData($gdo);
	}
	
	public function displayValue($var)
	{
	    $value = $this->toValue($var);
	    if ($this->multiple)
	    {
	        $value = array_map(function($gdo){ 
	            return $this->renderChoice($gdo); },
	            $value);
	        return implode(', ', $value);
	    }
	    return $this->renderChoice($value);
	}
	
	################
	### Validate ###
	################
	private function fixEmptyMultiple()
	{
	    $f = $this->formVariable();
		if (isset($_REQUEST[$f]) && $this->multiple)
		{
			if (!isset($_REQUEST[$f][$this->name]))
			{
				$_REQUEST[$f][$this->name] = [];
			}
		}
	}
	
	public function validate($value)
	{
		return $this->multiple ?
		  $this->validateMultiple($value) :
		  $this->validateSingle($value);
	}
	
	private function validateMultiple($values)
	{
	    if ($values)
	    {
    		foreach ($values as $value)
    		{
    			if (!$this->validateSingle($value))
    			{
    				return false;
    			}
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
			return $this->notNull ? $this->errorNotNull() : true;
		}
		
		
		if (is_object($value))
		{
    		if (isset($this->choices[$value->getID()])) # check memcached by id
    		{
    		    return true;
    		}
		}
		
		if (in_array($value, $this->choices, true)) # check single identity
		{
		    return true;
		}
		
		if (!$this->multiple)
		{
    		if (array_key_exists($this->toVar($value), $this->choices))
    		{
    		    return true;
    		}
		}
 		
 		return $this->errorInvalidChoice();
	}
	
	protected function errorInvalidChoice()
	{
		return $this->error('err_invalid_choice');
	}
	
	###############
	### Choices ###
	###############
	public $emptyValue = '0';
	public function emptyValue($emptyValue='0')
	{
		$this->emptyValue = $emptyValue;
		return $this->emptyLabel('please_choice');
	}
	
	public $emptyLabel;
	public $emptyLabelArgs;
	public function emptyLabel($emptyLabel, $args=null)
	{
		$this->emptyLabel = $emptyLabel;
		$this->emptyLabelArgs = $args;
		return $this;
	}
	
	public function displayEmptyLabel()
	{
		return t($this->emptyLabel, $this->emptyLabelArgs);
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
			if ($selected = @json_decode($this->getVar()))
			{
				if (in_array($value, $selected, true))
				{
					return self::SELECTED;
				}
			}
			return '';
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
	public function htmlMultiple() { return $this->multiple ? ' multiple="multiple"' : ''; }
	
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
	public function renderCell()
	{
		return GDT_Template::php('Form', 'cell/select.php', ['field' => $this]);
        return $this->renderForm();
	}
	
	public function renderForm()
	{
		return GDT_Template::php('Form', 'form/select.php', ['field' => $this]);
	}
	
	public function configJSON()
	{
		return array_merge(parent::configJSON(), [
			'multiple' => $this->multiple,
			'selected' => $this->multiple ? $this->getValue() : $this->getSelectedVar(),
			'minSelected' => $this->minSelected,
			'maxSelected' => $this->maxSelected,
		    'emptyValue' => $this->emptyValue,
		    'emptyLabel' => $this->displayEmptyLabel(),
		]);
	}
	
	public function getSelectedVar()
	{
		$var = $this->getVar();
		return $var === null ? $this->emptyValue : $var;
	}
	
	public function formName()
	{
	    $name = parent::formName();
	    return $this->multiple ? "{$name}[]" : $name;
	}
	
}
