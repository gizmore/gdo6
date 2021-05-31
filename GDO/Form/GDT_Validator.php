<?php
namespace GDO\Form;

use GDO\Core\GDT;

/**
 * A field that is an additional validator for a field.
 * A validator can be applied to a field and specify a method.
 * The method gets the form, the field, and the field's value to call error on the field.
 * 
 * @see \GDO\Register\Method\Form
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 5.0.0
 */
class GDT_Validator extends GDT
{
    public $writable = true;
    
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
	public function renderCLI() {}
	public function renderXML() {}
	
	/**
	 * 
	 */
	public function validator_func_dummy(GDT_Form $form, GDT $field, $value)
	{
		
	}
}
