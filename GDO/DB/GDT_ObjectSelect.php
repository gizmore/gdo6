<?php
namespace GDO\DB;
use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Select;
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
		return parent::renderForm();
	}
	
	public function renderJSON()
	{
		return array(
			'name' => $this->name,
			'multiple' => $this->multiple,
			'minSelected' => $this->minSelected,
			'maxSelected' => $this->maxSelected,
			'selected' => $this->multiple ? array_keys($this->getValue()) : $this->getSelectedVar(),
		);
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
	
	public function renderFilter()
	{
		return GDT_Template::php('DB', 'filter/object.php', ['field'=>$this]);
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
	
}
