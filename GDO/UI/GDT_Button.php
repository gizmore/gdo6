<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Form\GDT_Submit;

/**
 * A simple button.
 * 
 * @see GDT_Submit
 * @see GDT_Link
 * @see GDT_IconButton
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.1.0
 */
class GDT_Button extends GDT_Submit
{
	use WithHREF;
	use WithAnchorRelation;
	
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
	
	public function renderForm()
	{
	    return $this->renderCell();
	}
	
	public function renderJSON()
	{
	    return sprintf('<a href="%s">%s</a>', $this->gdoHREF(), $this->htmlIcon());
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
		return call_user_func([
			$this->gdo, 'href_' . $this->name]);
	}
	
	public function gdoLabel()
	{
		return call_user_func(
			[$this->gdo, 'display_'.$this->name]);
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
