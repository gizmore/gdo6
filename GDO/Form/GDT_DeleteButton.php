<?php
namespace GDO\Form;
use GDO\Core\GDT_Template;
/**
 * @author gizmore
 * @since 6.08
 * @version 6.10
 */
class GDT_DeleteButton extends GDT_Submit
{
	public $icon = 'delete';
	public function name($name=null) { $this->name = $name ? $name : 'delete'; return $this->defaultLabel(); }
	public function renderCell() { return GDT_Template::php('Form', 'form/delete.php', ['field'=>$this]); }
	public function defaultLabel() { return $this->label('btn_delete'); }
}
