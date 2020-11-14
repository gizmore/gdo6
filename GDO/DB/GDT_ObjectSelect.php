<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Select;

/**
 * A select WithObject trait.
 * It behaves a tiny bit different than GDT_Select, for the selected value.
 * It inits the choices with a call to $table->all()!
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
class GDT_ObjectSelect extends GDT_Select
{
	use WithObject;
	
	public function getChoices()
	{
		return $this->table->all();
	}
	
	public function initChoices()
	{
		return $this->choices === null ? $this->choices($this->getChoices()) : $this;
	}
	
	public function validate($value)
	{
		$this->initChoices();
		return parent::validate($value);
	}
	
	##############
	### Render ###
	##############
	public function renderForm()
	{
		$this->initChoices();
		if ($this->completionHref)
		{
		    return GDT_Template::php('DB', 'form/object_completion.php', ['field' => $this]);
		}
		return parent::renderForm();
	}
	
	public function renderCell()
	{
		if ($obj = $this->getValue())
		{
			# TODO: Multiple
			return $obj->renderCell();
		}
		return $this->getValue();
	}
	
	public function renderFilter($f)
	{
		return GDT_Template::php('DB', 'filter/object.php', ['field' => $this, 'f' => $f]);
	}
	
	#############
	### Value ###
	#############
	public function getVar()
	{
		return parent::getVar(); # required to overwrite trait.
	}
	
	public function toVar($value)
	{
		if ($value === null)
		{
			return null;
		}
		return $this->multiple ? $this->multipleToVar($value) : $value->getID();
	}
	
	/**
	 * @param GDO[] $value
	 * @return string
	 */
	public function multipleToVar(array $value)
	{
		$json = [];
		foreach ($value as $gdo)
		{
			$json[] = $gdo->getID();
		}
		return json_encode($json);
	}
	
	public function toValue($var)
	{
		return $this->multiple ? $this->getValueMulti($var) : $this->getValueSingle($var);
	}
	
	public function getValueSingle($id)
	{
		return $this->foreignTable()->find($id, false);
	}
	
	public function getValueMulti($var)
	{
		$back = [];
		foreach (json_decode($var) as $id)
		{
			if ($object = $this->table->find($id, false))
			{
				$back[$id] = $object;
			}
		}
		return $back;
	}
	
	##############
	### Config ###
	##############
	private function configJSONSelected()
	{
	    if ($this->multiple)
	    {
	        $selected = [];
	        foreach ($this->getValue() as $value)
	        {
	            $selected[] = array(
	                'id' => $value->getID(),
	                'text' => $value->displayName(),
	                'display' => $value->renderChoice(),
	            );
	        }
	    }
	    else
	    {
	        if ($value = $this->getValue())
	        {
    	        $selected = array(
    	            'id' => $value->getID(),
    	            'text' => $value->displayName(),
    	            'display' => $value->renderChoice(),
    	        );
	        }
	        else
	        {
	            $selected = array(
	                'id' => $this->emptyValue,
	                'text' => $this->displayEmptyLabel(),
	                'display' => $this->displayEmptyLabel(),
	            );
	        }
	    }
	    return $selected;
	}
	
	public function configJSON()
	{
	    return array_merge(parent::configJSON(), array(
	        'selected' => $this->configJSONSelected(),
	    ));
	}
	
}
