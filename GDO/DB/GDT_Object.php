<?php
namespace GDO\DB;
use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\Core\WithCompletion;

class GDT_Object extends GDT_UInt
{
	use WithObject;
	use WithCompletion;
	
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
	
	 public function renderChoice($choice)
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
	### Config ###
	##############
	public function configJSON()
	{
	    $gdo = $this->getValue();
	    return array_merge(parent::configJSON(), array(
	        'selected' => ($gdo ? array(
	            'id' => $gdo->getID(),
	            'display' => $gdo->displayName(),
	        ) : null),
	        'completionHref' => $this->completionHref,
	    ));
	}
	
	
}
