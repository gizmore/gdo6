<?php
namespace GDO\Form;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
use GDO\UI\WithTitle;
use GDO\UI\GDT_SearchField;

/**
 * An HTML Form.
 * @author gizmore
 * @version 6.10
 * @since 3.00
 */
class GDT_Form extends GDT
{
	public static $VALIDATING_INSTANCE; # ugly, but hey.
	public static $VALIDATING_SUCCESS; # ugly, but hey.
	public static $CURRENT; # ugly, but hey?! performance :)
	
	use WithFields;
	use WithTitle;
	
	public function __construct()
	{
		$this->action = @$_SERVER['REQUEST_URI'];
		$this->writable = false;
	}
	
	############
	### Info ###
	############
	public $info;
	public function info($info) { $this->info = $info; return $this; }
	
	##############
	### Method ###
	##############
	public $method = 'POST';
	public function method($method) { $this->method = $method; return $this; }
	public function methodGET() { return $this->method('GET'); }
	public function methodPOST() { return $this->method('POST'); }
	
	################
	### Encoding ###
	################
	const MULTIPART = 'multipart/form-data';
	const URLENCODED = 'application/x-www-form-urlencoded';
	public $encoding = self::URLENCODED;
	public function encoding($encoding) { $this->encoding = $encoding; return $this; }
	
	##############
	### Action ###
	##############
	public $action;
	public function action($action=null) { $this->action = $action; return $this; }
	
	##############
	### Layout ###
	##############
	public $slim = false;
	public function slim($slim=true) { $this->slim = $slim; return $this; }
	public function htmlClassSlim() { return $this->slim ? 'gdo-form-slim' : 'gdo-form-large'; }

	##############
	### Render ###
	##############
	public function renderCell()
	{
		self::$CURRENT = $this;
		$this->withGDOValuesFrom($this->gdo);
		$back = GDT_Template::php('Form', 'cell/form.php', ['form' => $this]);
		self::$CURRENT = null;
		return $back;
	}
	
	public function reset(GDO $gdo)
	{
	    $this->withFields(function(GDT $gdt) use ($gdo) { $gdt->gdo($gdt->gdo);; });
	}
	
	################
	### Validate ###
	################
	public $validated = false;
	
	public function validateForm()
	{
	    self::$CURRENT = $this;
	    self::$VALIDATING_INSTANCE = $this;
		self::$VALIDATING_SUCCESS = true;
		$this->validateFormField($this);
		self::$CURRENT = null;
		return self::$VALIDATING_SUCCESS;
	}
	
	public function validateFormField(GDT $field)
	{
	    # Check field
		if (($field->writable) && (!$field->error))
		{
		    $var = $field->getVar();
// 			$value = $field->getValidationValue();
			if (!$field->validate($field->toValue($var)))
			{
				self::$VALIDATING_SUCCESS = false;
				if (!$field->error)
				{
					$field->error('err_field_errorneus');
				}
			}
			else
			{
				$field->var($var);
			}
		}
		# Recursive
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
	
	#############
	### Build ###
	#############
	public function withGDOValuesFrom(GDO $gdo=null)
	{
		$this->fieldWithGDOValuesFrom($this, $gdo);
		return $this;
	}
	
	private function fieldWithGDOValuesFrom(GDT $gdoType, GDO $gdo=null)
	{
	    if ($gdo === null)
	    {
	        $gdoType->var($gdoType->initial);
	    }
	    else
	    {
	        $gdoType->gdo($gdo);
	    }
		if ($fields = $gdoType->getFields())
		{
			foreach ($fields as $field)
			{
				$this->fieldWithGDOValuesFrom($field, $gdo);
			}
		}
	}
	
	private static $formData; # ugly
	public function getFormData()
	{
		self::$formData = [];
		$this->withFields(function(GDT $field) {
			if ($data = $field->getGDOData())
			{
			    foreach ($data as $k => $v)
			    {
    			    self::$formData[$k] = $v;
			    }
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

	##########################
	### Auto hidden fields ###
	##########################
	public function htmlHidden()
	{
	    $back = '';
	    $back = $this->htmlHiddenRec($_GET, $back);
	    return $back;
	}

	private function htmlHiddenRec($getVars, $out)
	{
	    foreach ($getVars as $k => $v)
	    {
	        if (is_array($v))
	        {
	            $out = $this->htmlHiddenRec($v, $out);
	        }
	        elseif (!$this->hasField($k))
	        {
	            $out .= sprintf('<input type="hidden" name="%s" value="%s" />', html($k), html($v));
	        }
	    }
	    return $out;
	}
	
	public static function hiddenMoMe()
	{
	    return sprintf('<input type="hidden" name="mo" value="%s" /><input type="hidden" name="me" value="%s" />',
	        html(@$_REQUEST['mo']), html(@$_REQUEST['me']));
	}

	###############
	### Display ###
	###############
	/**
	 * Display a label with filter criteria.
	 * @return string
	 */
	public function displaySearchCriteria()
	{
	    $data = [];
	    foreach ($this->getFieldsRec() as $gdt)
	    {
	        if ($gdt->filterable || $gdt->searchable || $gdt->orderable || ($gdt instanceof GDT_SearchField))
	        {
    	        if (!($var = $gdt->filterVar($this->name)))
    	        {
    	            $var = $gdt->getVar();
    	        }
    	        if ($var)
    	        {
    	            $data[] = sprintf('%s: %s', $gdt->displayLabel(), $gdt->displayValue($var));
    	        }
	        }
	    }
	    return t('lbl_search_criteria', [implode(', ', $data)]);
	}

}
