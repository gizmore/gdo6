<?php
namespace GDO\DB;
use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\Core\WithCompletion;

class GDT_Object extends GDT_UInt
{
	use WithObject;
	use WithCompletion;
	
	public function htmlClass()
	{
		return ' gdt-object';
	}
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
		if ($obj = $this->getValue())
		{
			return $obj->renderCell();
		}
		return $this->getVar();
	}
	
	 public function renderChoice($choice=null)
	 {
	     /** @var $obj GDO **/
		 if ($obj = $this->getValue())
		 {
			 return $obj->renderChoice();
		 }
	 }
	
	public function renderForm()
	{
		if ($this->completionHref)
		{
			return GDT_Template::php('DB', 'form/object_completion.php', ['field'=>$this]);
		}
		else
		{
			return GDT_Template::php('DB', 'form/object.php', ['field'=>$this]);
		}
	}
	
	##############
	### Filter ###
	##############
	public function filterVar($rq=null)
	{
		return $this->_getRequestVar("{$rq}[f]", null, $this->filterField ? $this->filterField : $this->name);
	}
	
	##############
	### Config ###
	##############
	public function configJSON()
	{
	    if ($gdo = $this->getValue())
	    {
	        $selected = [
	            'id' => $gdo->getID(),
	            'text' => $gdo->displayName(),
	            'display' => json_quote($gdo->renderChoice()),
	        ];
	    }
	    else 
	    {
	        $selected = [
	            'id' => null,
	            'text' => $this->placeholder,
	            'display' => $this->placeholder,
	        ];
	    }
	    return array_merge(parent::configJSON(), [
	        'cascade' => $this->cascade,
	        'selected' => $selected,
	        'completionHref' => $this->completionHref,
	    ]);
	}
	
}
