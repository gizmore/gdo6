<?php
namespace GDO\UI;
class GDT_EditButton extends GDT_Button
{
	public function defaultLabel() { return $this->label('btn_edit'); }
	
	public function name($name=null)
	{
		return $name ? parent::name($name) : $this;
	}
	
	protected function __construct()
	{
	    parent::__construct();
		$this->name = "edit";
		$this->icon('edit');
	}
}
