<?php
namespace GDO\Form;

use GDO\Core\GDT_Template;

/**
 * A delete button confirms before the action is executed.
 *
 * @author gizmore
 * @version 6.10
 * @since 6.08
 */
class GDT_DeleteButton extends GDT_Submit
{
	public $icon = 'delete';
	public function name($name=null) { $this->name = $name ? $name : 'delete'; return $this->defaultLabel(); }
	public function renderCell() { return GDT_Template::php('Form', 'form/delete.php', ['field'=>$this]); }
	public function defaultLabel() { return $this->label('btn_delete'); }
	public function defaultName() { return 'delete'; }

	############
	### Text ###
	############
	public $confirmKey = 'confirm_delete';
	public $confirmArgs = null;
	public function confirmText($key, array $args=null)
	{
	    $this->confirmKey = $key;
	    $this->confirmArgs = $args;
	    return $this;
	}
	public function displayConfirmText()
	{
	    return t($this->confirmKey, $this->confirmArgs);
	}

}
