<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Form\WithFormFields;

/**
 * A simple button.
 * 
 * @see GDT_Submit
 * @see GDT_Link
 * @see GDT_IconButton
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.1.0
 */
class GDT_Button extends GDT_Label
{
	use WithHREF;
	use WithIcon;
	use WithFormFields;
	use WithPHPJQuery;
	use WithAnchorRelation;
	
	public $writable = true;
	
	public $primaryButton = true;
	public function primary() { $this->primaryButton = true; return $this; }
	public function secondary() { $this->primaryButton = false; return $this; }
	
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
	    if ($this->href)
	    {
	        return $this->href;
	    }
		return call_user_func([$this->gdo, 'href_' . $this->name]);
	}
	
	public function gdoLabel()
	{
		return call_user_func([$this->gdo, 'display_'.$this->name]);
	}
	
	########################
	### Enabled callback ###
	########################
	public $checkEnabled;
	public function checkEnabled(callable $checkEnabled)
	{
	    $this->checkEnabled = call_user_func($checkEnabled);
	    return $this;
	}

}
