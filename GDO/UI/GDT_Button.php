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

    ##############
	### Render ###
	##############
	public function htmlClass()
	{
		return sprintf(' class="gdo-button %s"', str_replace('\\', '-', strtolower(get_class($this))));
	}
	
	public function renderForm()
	{
		return GDT_Template::php('UI', 'form/button.php', ['field'=>$this]);
	}
	
	public function gdoHREF()
	{
		return $this->href ? $this->href : call_user_func(array($this->gdo, 'href_'.$this->name));
	}
	
	public function gdoLabel()
	{
		return call_user_func(array($this->gdo, 'display_'.$this->name));
	}
	
	public function renderCell()
	{
		$href = $this->gdoHREF();
		return GDT_Template::php('UI', 'cell/button.php', ['field'=>$this, 'href'=>$href]);
	}

	public function displayHeaderLabel()
	{
		return '';
	}
}

