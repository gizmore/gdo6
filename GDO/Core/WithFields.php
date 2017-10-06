<?php
namespace GDO\Core;
/**
 * Used by types that have collections of fields.
 * E.g.: GDT_Bar, GDO, GDT_Form
 * @author gizmore
 * @version 6.05
 */
trait WithFields
{
    ##############
    ### Fields ###
    ##############
    /**
     * @var GDT[]
     */
    public $fields = [];
    public function addField(GDT $field) { $this->fields[$field->name] = $field; return $this; }
    public function addFields(array $fields=null)
    {
        if ($fields)
        {
            foreach ($fields as $field)
            {
                $this->fields[$field->name] = $field;
            }
        }
        return $this;
    }
    /**
     * @param string $key
     * @return GDT
     */
    public function getField($name) { return @$this->fields[$name]; }
    public function getFields() { return $this->fields; }
    public function removeField($name) { unset($this->fields[$name]); }
    public function removeFields() { $this->fields = []; }

    ################
    ### Iterator ###
    ################
    public function withFields($callback) { $this->_withFields($this, $callback); }
    public function _withFields(GDT $field, $callback)
    {
    	call_user_func($callback, $field);
    	if ($fields = $field->getFields())
    	{
    		foreach ($fields as $field)
    		{
    			$this->_withFields($field, $callback);
    		}
    	}
    }
}
