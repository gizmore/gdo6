<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Form\WithFormFields;

/**
 * @author gizmore
 * @version 6.10
 * @since 6.01
 * @see GDT_Submit
 * @see GDT_Link
 * @see GDT_IconButton
 */
class GDT_Button extends GDT_Label
{
	use WithHREF;
	use WithIcon;
	use WithFormFields;
	use WithPHPJQuery;
	use WithAnchorRelation;
	
// 	public $primary = true;
// 	public function primary() { $this->primary = true; return $this; }
// 	public function secondary() { $this->primary = false; return $this; }
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    if ($this->checkEnabled)
	    {
    	    $this->writable(call_user_func($this->checkEnabled, $this));
	    }
		return GDT_Template::php('UI', 'cell/button.php', ['field'=>$this, 'href'=>$this->gdoHREF()]);
	}

	#############
	### Proxy ###
	#############
	public function gdoHREF()
	{
		return $this->href ? $this->href : call_user_func([$this->gdo, 'href_'.$this->name]);
	}
	
	public function gdoLabel()
	{
		return call_user_func([$this->gdo, 'display_'.$this->name]);
	}
	
	public function displayHeaderLabel()
	{
		return '';
	}
	
	########################
	### Enabled callback ###
	########################
	public $checkEnabled;
	public function checkEnabled(callable $checkEnabled)
	{
	    $this->checkEnabled = $checkEnabled;
	    return $this;
	}

}
