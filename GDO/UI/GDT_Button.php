<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;
use GDO\Form\WithFormFields;
/**
 * @author gizmore
 * @version 6.05
 */
class GDT_Button extends GDT_Label
{
    use WithHREF;
    use WithIcon;
    use WithFormFields;
    use WithPHPJQuery;
    
    public $primary = true;
    public function primary() { $this->primary = true; return $this; }
	public function secondary() { $this->primary = false; return $this; }
    
    ##############
	### Render ###
	##############
	public function renderCell()
	{
		return GDT_Template::php('UI', 'cell/button.php', ['field'=>$this, 'href'=>$this->gdoHREF()]);
	}

// 	public function renderForm()
// 	{
// 		return GDT_Template::php('UI', 'form/button.php', ['field'=>$this]);
// 	}

	#############
	### Proxy ###
	#############
	public function gdoHREF()
	{
		return $this->href ? $this->href : call_user_func(array($this->gdo, 'href_'.$this->name));
	}
	
	public function gdoLabel()
	{
		return call_user_func(array($this->gdo, 'display_'.$this->name));
	}
	
	public function displayHeaderLabel()
	{
		return '';
	}
}

