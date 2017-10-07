<?php
namespace GDO\Form;
trait WithFormFields
{
    public $cssClass;
    public function cssClass($cssClass) { $this->cssClass = $cssClass; return $this; }
    
    public $inlineJS;
    public function inlineJS($inlineJS) { $this->inlineJS = $inlineJS; return $this; }
    
    public $required = false;
    public function required($required=true) { $this->required = $required; return $this; }
    public function htmlRequired() { return $this->isRequired() ? ' required="required"' : ''; }
    public function isRequired() { return $this->required || (isset($this->notNull)&&$this->notNull); }
    
    public $disabled = false;
    public function disabled($disabled=true) { $this->disabled = $disabled; return $this; }
    public function htmlDisabled() { return $this->disabled ? ' disabled="disabled"' : ''; }

    public function changeRequestVar($value)
    {
        $_REQUEST['form'][$this->name] = $value;
        $_POST['form'][$this->name] = $value;
    }
    
}
