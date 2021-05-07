<?php
namespace GDO\Form;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
use GDO\UI\WithPHPJQuery;

/**
 * Form submit button.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 1.0.0
 */
class GDT_Submit extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithFormFields;
	use WithPHPJQuery;
	
	public $writable = true;

	public function defaultName() { return 'submit'; }
	
	public function renderCell() { return $this->renderForm(); }
	public function renderForm() { return GDT_Template::php('Form', 'form/submit.php', ['field'=>$this]); }

}
