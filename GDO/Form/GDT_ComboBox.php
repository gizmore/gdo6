<?php
namespace GDO\Form;
use GDO\Core\WithCompletion;
use GDO\DB\GDT_String;
/**
 * A combobox is a string with additional completion and dropdown.
 * @author gizmore
 * @since 6.00
 * @version 6.09
 * @see GDT_Select
 */
class GDT_ComboBox extends GDT_String
{
	use WithCompletion;
	
	public $choices = null;
	public function choices(array $choices)
	{
		$this->choices = $choices;
		return $this;
	}
}
