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
	const PRIMARY = 0;
	const SECONDARY = 1;
	const UNADVISED = 2;
	
	public $priority = self::PRIMARY;
	
	public function primary() { return $this->priority(self::PRIMARY); }
	public function secondary() { return $this->priority(self::SECONDARY); }
	public function unadvised() { return $this->priority(self::UNADVISED); }
	public function priority($priority) { $this->priority = $priority; return $this; }
	
}
