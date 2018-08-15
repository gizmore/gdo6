<?php
namespace GDO\UI;
class GDT_DeleteButton extends GDT_Button
{
	public function defaultLabel() { return $this->label('delete'); }
	
	public function name($name=null)
	{
		return $name ? parent::name($name) : $this;
	}
	
	public function __construct()
	{
		$this->name = "delete";
		$this->icon('delete');
	}
}
