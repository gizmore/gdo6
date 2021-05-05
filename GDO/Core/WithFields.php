<?php
namespace GDO\Core;

/**
 * Used by types that have collections of fields.
 * E.g.: GDT_Fields, GDT_Bar, GDT_Response, GDT_Form, GDT_Table->headers.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.0.0
 * 
 * @see GDT_Bar
 * @see GDT_Table
 */
trait WithFields
{
	###############
	### Factory ###
	###############
	/**
	 * @param GDT ...$fields
	 * @return self
	 */
	public static function makeWith(GDT ...$fields)
	{
		return self::make()->addFields($fields);
	}
	
	##############
	### Fields ###
	##############
	/**
	 * @var \GDO\Core\GDT[]
	 */
	public $fields = [];
	public function addField(GDT $field=null)
	{
	    if ($field)
	    {
	        if ($field->name)
	        {
    	        $this->fields[$field->name] = $field;
	        }
	        else
	        {
	            $this->fields[] = $field;
	        }
	    }
        return $this;
	}
	
	public function addFields(array $fields=null)
	{
		if ($fields)
		{
			foreach ($fields as $gdt)
			{
			    $this->addField($gdt);
			}
		}
		return $this;
	}
	
	public function addFieldAfter(GDT $field, $after)
	{
	    $newFields = [];
	    $added = false;
	    foreach ($this->fields as $name => $gdt)
	    {
	        $newFields[$name] = $gdt;
	        if ($name === $after)
	        {
	            $newFields[$field->name] = $field;
	            $added = true;
	        }
	    }
	    if (!$added)
	    {
	        $newFields[$field->name] = $field;
	    }
	    $this->fields = $newFields;
	    return $this;
	}
	
	public function addFieldFirst(GDT $field)
	{
	    $newFields = $field->name ? [$field->name => $field] : [$field];
	    $this->fields = array_merge($newFields, $this->fields);
	    return $this;
	}
	
	public function clearFields()
	{
	    $this->fields = [];
	    return $this;
	}
	
	/**
	 * Return all fields in this collection.
	 * @return \GDO\Core\GDT[]
	 */
	public function &getFields() { return $this->fields; }

	public function fieldCount() { return $this->fields ? count($this->fields) : 0; }
	
	public function searchableFieldCount()
	{
	    $count = 0;
	    if ($this->fields)
	    {
	        foreach ($this->fields as $gdt)
	        {
	            $count += $gdt->searchable ? 1 : 0;
	        }
	    }
	    return $count;
	}
	
	public function orderableFieldCount()
	{
	    $count = 0;
	    if ($this->fields)
	    {
	        foreach ($this->fields as $gdt)
	        {
	            $count += $gdt->orderable ? 1 : 0;
	        }
	    }
	    return $count;
	}
	
    /**
	 * Return a field by name.
	 * @param string $name
	 * @return \GDO\Core\GDT
	 */
	public function getField($name) { return $this->fields[$name]; }
	public function hasField($name) { return isset($this->fields[$name]); }
	public function hasFields() { return count($this->fields) > 0; }
	public function removeField($name) { unset($this->fields[$name]); }
	public function removeFields() { $this->fields = []; }

	################
	### Iterator ###
	################
	public function withFields($callback) { $this->_withFields($this, $callback); }
	private function _withFields(GDT $field, $callback)
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
	
	##############
	### Render ###
	##############
	public function renderCard()
	{
	    if ($this->fields)
	    {
    	    foreach($this->fields as $gdt)
    	    {
    	        echo $gdt->renderCard();
    	    }
	    }
	}
	
	public function renderJSON()
	{
		$json = [];
		foreach ($this->getFieldsRec() as $gdoType)
		{
		    if ($this->gdo)
		    {
		        $gdoType->gdo($this->gdo);
		    }
			if ($data = $gdoType->renderJSON())
			{
			    if (is_array($data))
			    {
    			    foreach ($data as $k => $v)
    			    {
    			        $json[$k] = $v;
    			    }
			    }
			    else
			    {
			        $json[$gdoType->name] = $data;
			    }
			}
		}
		return $json;
	}

	##############################
	### Get Fields Recursively ###
	##############################
	/**
	 * @return GDT[]
	 */
	public function getFieldsRec()
	{
		$fields = [];
		$this->_getFieldsRec($fields, $this);
		return $fields;
	}
	
	private function _getFieldsRec(array &$fields, GDT $gdt)
	{
		foreach ($gdt->fields as $_gdt)
		{
			$fields[$_gdt->name] = $_gdt;
			$uses = class_uses($_gdt);
			if (in_array('GDO\Core\WithFields', $uses, true))
			{
				$this->_getFieldsRec($fields, $_gdt);
			}
		}
	}
	
}
