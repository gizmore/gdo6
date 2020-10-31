<?php
namespace GDO\Form;
use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
use GDO\UI\WithPHPJQuery;
/**
 * Form submit button.
 * @author gizmore
 * @since 1.00
 * @version 6.05
 */
class GDT_Submit extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithFormFields;
	use WithPHPJQuery;
	
	public $editable = false;

// 	public function defaultLabel() { return $this->label('btn_send'); }
	
	public function isSerializable() { return false; }
	
	public function name($name=null) { $this->name = $name ? $name : 'submit'; return $this->defaultLabel(); }
	public function renderCell() { return GDT_Template::php('Form', 'cell/submit.php', ['field'=>$this]); }
	public function renderForm() { return GDT_Template::php('Form', 'form/submit.php', ['field'=>$this]); }
}
