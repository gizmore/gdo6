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
 * @version 6.11.2
 * @since 6.2.0
 */
class GDT_ObjectSelect extends GDT_Select
{
	use WithObject;
	
	public function getChoices()
	{
		return $this->table ? $this->table->allCached() : [];
	}
	
	public function initChoices()
	{
		return $this->choices($this->getChoices());
	}
	
	public function validate($value)
	{
		$this->initChoices();
        if ($value === null)
        {
            if ($this->notNull)
            {
                if ($this->getVar())
                {
                    return $this->errorNotFound();
                }
                return $this->errorNotNull();
            }
            return true;
        }
        
        if (!$this->getValue())
        {
            return $this->errorInvalidChoice();
        }
        
		return true;
	}
	
	public function errorNotFound()
	{
	    return $this->error('err_gdo_not_found', [
	        $this->foreignTable()->gdoHumanName(), html($this->getVar())]);
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
			if (is_array($obj))
			{
				$back = '';
				foreach ($obj as $gdo)
				{
					$back .= $gdo->renderCell();
				}
				return $back;
			}
			return $obj->renderCell();
		}
		return $obj;
	}
	
	public function renderJSON()
	{
		/**
		 * @var $value GDO
		 */
		if ($value = $this->getValue())
		{
			if (is_array($value))
			{
				$json = [];
				foreach ($value as $obj)
				{
					$json[] = $obj->toJSON();
				}
				return $json;
			}
			else
			{
				return $value->toJSON();
			}
		}
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
		return array_map(function($gdo) {
			return $gdo->getID();
		}, $value);
	}
	
	public function toValue($var)
	{
	    if ($this->foreignTable())
	    {
    		return $this->multiple ? $this->getValueMulti($var) : $this->getValueSingle($var);
	    }
	    return $this->multiple ? [] : null;
	}
	
	public function getValueSingle($id)
	{
		return $this->foreignTable()->find($id, false);
	}
	
	public function getValueMulti($var)
	{
		$back = [];
		
		if (!is_array($var))
		{
		    $var = json_decode($var);
		}
		
		foreach ($var as $id)
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
