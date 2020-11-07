<?php
namespace GDO\Form;
use GDO\UI\GDT_IconButton;

/**
 * An add button with a plus.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
class GDT_AddButton extends GDT_IconButton
{
	public $icon = 'plus';
	public function name($name=null) { $this->name = $name ? $name : 'btn_add'; return $this->label($name); }
}
