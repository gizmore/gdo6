<?php
namespace GDO\Core;

/**
 * Used by types that have collections of fields.
 * E.g.: GDT_Fields, GDT_Bar, GDT_Response, GDT_Form, GDT_Table->headers.
 * Can invoke rendering on it's fields.
 * 
 * @author gizmore
 * @version 6.10.4
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
	 * @var GDT[]
	 */
	public $fields = [];
	
	public function addField(GDT $field=null)
	{
	    return $this->_addField($field);
	}
	
	protected function _addField(GDT $field=null)
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
	public function renderCLI()
	{
	    return $this->renderCLIFields();
	}
	
	public function renderCLIFields()
	{
	    $back = [];
	    foreach ($this->fields as $field)
	    {
	        if ($field->cli)
	        {
	            if ($text = $field->renderCLI())
	            {
	                $back[] = $text;
	            }
	        }
	    }
	    return implode(', ', $back);
	}
	
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
	    return $this->renderJSONFields();
	}
	
	public function renderJSONFields()
	{
		$json = [];
		foreach ($this->getFieldsRec() as $gdt)
		{
		    if ($this->gdo)
		    {
		        $gdt->gdo($this->gdo);
		    }
			if ($data = $gdt->renderJSON())
			{
		        $json[$gdt->name] = [
		            'var' => $gdt->var,
		            'display' => $data,
		        ];
		        if ($gdt->error)
		        {
		            $json[$gdt->name]['error'] = $gdt->error;
		        }
			}
		}
		return $json;
	}
	
	public function renderXML()
	{
	    $xml = '';
	    if ($this->name)
	    {
	        $var = html($this->getVar());
    	    $xml = sprintf("<{$this->name} var=\"%s\">\n", $var);
            $xml .= $this->renderXMLFields();
    	    $xml .= "</{$this->name}>\n";
	    }
	    return $xml;
	}
	
	public function renderXMLFields()
	{
	    $xml = '';
	    if ($this->fields)
	    {
	        foreach ($this->fields as $gdt)
	        {
	            $xml .= $gdt->renderXML();
	        }
	    }
	    return $xml;
	}

	##############################
	### Get Fields Recursively ###
	##############################
	/**
	 * @return GDT[]
	 */
	public function getFieldsRec()
	{
		return $this->_getFieldsRec($this);
	}
	
	private function _getFieldsRec(GDT $gdt)
	{
	    $fields = [];
		foreach ($gdt->fields as $_gdt)
		{
		    if ($_gdt->name)
		    {
    			$fields[$_gdt->name] = $_gdt;
		    }
		    else
		    {
		        $fields[] = $_gdt;
		    }
			if (isset($_gdt->fields))
			{
			    $fields = array_merge($fields,
    			    $this->_getFieldsRec($_gdt)
		        );
			}
		}
		return $fields;
	}
	
}
