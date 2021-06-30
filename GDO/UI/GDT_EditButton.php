<?php
namespace GDO\UI;

class GDT_EditButton extends GDT_Button
{
    public function defaultName() { return 'edit'; }
    public function defaultLabel() { return $this->label('btn_edit'); }
    
	public $icon = 'edit';
	public $editable = false;
	
}
