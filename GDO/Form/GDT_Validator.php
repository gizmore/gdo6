<?php
namespace GDO\Form;

use GDO\Core\GDT;

/**
 * A field that is an additional validator for a field.
 * 
 * @see \GDO\Register\Method\Form
 * 
 * @author gizmore
 * @since 3.0
 * @version 6.07
 */
class GDT_Validator extends GDT
{
	public $validator;
	public $validateField;
	public function validator($fieldName, $validator) { $this->validateField = $fieldName;  $this->validator = $validator; return $this; }
	
	public function validate($value)
	{
		$form = GDT_Form::$VALIDATING_INSTANCE;
		$field = $this->validatorField();
		if (!call_user_func($this->validator, $form, $field, $field->getValue()))
		{
			GDT_Form::$VALIDATING_SUCCESS = false;
		}
		return true;
	}
	public function renderCell() { return ''; }
	public function validatorField() { return GDT_Form::$VALIDATING_INSTANCE->fields[$this->validateField]; }
	public function renderJSON() {}
	
	/**
	 * 
	 */
	public function validator_func_dummy(GDT_Form $form, GDT $field, $value)
	{
		
	}
}
