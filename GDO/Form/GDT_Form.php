<?php
namespace GDO\Form;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
use GDO\UI\WithTitle;
use GDO\UI\GDT_Panel;
use GDO\UI\WithHTML;
class GDT_Form extends GDT
{
	public static $VALIDATING_INSTANCE; # ugly, but hey.
	public static $VALIDATING_SUCCESS; # ugly, but hey.
	public static $CURRENT; # ugly, but hey?! performance :)
	
	public $validated = false;
	
	use WithFields;
	use WithTitle;
	
	public function __construct()
	{
//	 	$this->name = 'form';
		$this->action = $_SERVER['REQUEST_URI'];
	}
	
	public $info;
	public function info($info) { $this->info = $info; return $this; }
	
	public $method = 'POST';
	public function method($method)
	{
		$this->method = $method;
		return $this;
	}
	
	const URLENCODED = 'application/x-www-form-urlencoded';
	const MULTIPART = 'multipart/form-data';
	public $encoding = self::MULTIPART;
	public function encoding($encoding) { $this->encoding = $encoding; return $this; }
	
	public $action;
	public function action($action=null) { $this->action = $action; return $this; }
	
	public function render()
	{
		self::$CURRENT = $this;
		return GDT_Template::php('Form', 'cell/form.php', ['form' => $this]);
	}
	
	public function renderJSON()
	{
		$json = [];
		foreach ($this->fields as $field)
		{
			if ($j = $field->renderJSON())
			{
				$json[$field->name] = $j;
//	 			$json = array_merge($json, $j);
			}
		}
		return $json;
	}
	
	public function validateForm()
	{
		self::$VALIDATING_INSTANCE = $this;
		self::$VALIDATING_SUCCESS = true;
		$this->validateFormField($this);
		return self::$VALIDATING_SUCCESS;
	}
	
	public function validateFormField(GDT $field)
	{
		if (($field->writable) && (!$field->error))
		{
			$value = $field->getValidationValue();
			if (!$field->validate($value))
			{
				self::$VALIDATING_SUCCESS = false;
				if (!$field->error)
				{
					# Validators have to return truthy
					$field->error('err_field_errorneus');
				}
			}
			else
			{
				$field->value($value);
			}
		}
		if ($fields = $field->getFields())
		{
			foreach ($fields as $field)
			{
				$this->validateFormField($field);
			}
		}
	}
	
	public function onValidated()
	{
		$this->validated = true;
		foreach ($this->fields as $field)
		{
			$field->onValidated();
		}
	}
	
	public function withGDOValuesFrom(GDO $gdo=null)
	{
		$this->fieldWithGDOValuesFrom($this, $gdo);
		return $this;
	}
	
	public function fieldWithGDOValuesFrom(GDT $gdoType, GDO $gdo=null)
	{
		$gdoType->gdo($gdo);
		if ($fields = $gdoType->getFields())
		{
			foreach ($fields as $field)
			{
				$this->fieldWithGDOValuesFrom($field, $gdo);
			}
		}
	}
	
	private static $formData;
	public function getFormData()
	{
		self::$formData = array();
		$this->withFields(function(GDT $field){
			if ($data = $field->getGDOData())
			{
				self::$formData = array_merge(self::$formData, $data);
			}
		});
		return self::$formData;
	}

	public function getFormVar($key)
	{
		return isset($this->fields[$key]) ? $this->fields[$key]->getVar() : null;
	}
	
	public function getFormValue($key)
	{
		return isset($this->fields[$key]) ? $this->fields[$key]->getValue() : null;
	}

}
