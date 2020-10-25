<?php
namespace GDO\Form;

/**
 * Form fields have required and disabled attribute.
 * They also can have inline javascript.
 *  
 * @author gizmore
 * @version 6.10
 * @since 6.01
 * 
 * @see WithPHPJQuery
 */
trait WithFormFields
{
// 	public $cssClass;
// 	public function cssClass($cssClass) { $this->cssClass = $cssClass; return $this; }
	
	public $inlineJS;
	public function inlineJS($inlineJS) { $this->inlineJS = $inlineJS; return $this; }
	
// 	public $required = false;
	public function required($required=true) { $this->notNull = $required; return $this; }
	public function htmlRequired() { return $this->notNull ? ' required="required"' : ''; }
// 	public function isRequired() { return $this->required || (isset($this->notNull)&&$this->notNull); }
	
// 	public $disabled = false;
	public function enabled($enabled=true) { return $this->writable($enabled); }
	public function disabled($disabled=true) { return $this->writable(!$disabled); }
	public function htmlDisabled() { return $this->writable ? '' : ' disabled="disabled"'; }

	/**
	 * Change a request var globally on the fly.
	 * 
	 * @deprecated
	 * @param string $value
	 */
	public function changeRequestVar($value=null)
	{
	    if ($value === null)
	    {
	        unset($_REQUEST['form'][$this->name]);
	        unset($_POST['form'][$this->name]);
	    }
	    else
	    {
    		$_REQUEST['form'][$this->name] = $value;
    		$_POST['form'][$this->name] = $value;
	    }
	}
	
}
