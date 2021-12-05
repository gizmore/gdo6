<?php
namespace GDO\Form;

use GDO\Core\WithCompletion;
use GDO\DB\GDT_String;
use GDO\Core\GDT_Template;

/**
 * A combobox is a string with additional completion and dropdown.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 * 
 * @see GDT_Select
 */
class GDT_ComboBox extends GDT_String
{
	use WithCompletion;
	
	public $choices = [];
	public function choices(array $choices)
	{
		$this->choices = $choices;
		return $this;
	}
	
	public function configJSON()
	{
	    return array_merge(parent::configJSON(), array(
	        'selected' => [
	            'id' => $this->getVar(),
	            'text' => $this->getVar(),
	            'display' => $this->display(),
	        ],
	        'completionHref' => $this->completionHref,
	        'combobox' => 1,
	    ));
	}
	
	public function renderForm()
	{
	    return GDT_Template::php('Form', 'form/combobox.php', ['field' => $this]);
	}
	
}
