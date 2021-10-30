<?php
namespace GDO\Form;

use GDO\Core\GDT_Template;
use GDO\UI\WithIcon;
use GDO\UI\WithPHPJQuery;
use GDO\UI\GDT_Label;

/**
 * Form submit button.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 1.0.0
 */
class GDT_Submit extends GDT_Label
{
	use WithIcon;
	use WithFormFields;
	use WithPHPJQuery;
	
	public $writable = true;
	public $groupable = false;

	public function defaultName() { return 'submit'; }
	
	public function renderCell() { return $this->renderForm(); }
	public function renderForm() { return GDT_Template::php('Form', 'form/submit.php', ['field'=>$this]); }

    #################
	### Secondary ###
	#################
	public $primaryButton = true;
	public function primary() { $this->primaryButton = true; return $this; }
	public function secondary() { $this->primaryButton = false; return $this; }
	
}
