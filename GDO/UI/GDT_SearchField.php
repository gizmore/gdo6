<?php
namespace GDO\UI;
use GDO\DB\GDT_String;
use GDO\Core\GDT_Template;
/**
 * @author gizmore
 */
class GDT_SearchField extends GDT_String
{
	public $_inputType = 'search';
	public function defaultLabel() { return $this->label('search'); }
	public $min = 3;
	public $max = 128;
	public function __construct()
	{
		$this->icon('search');
	}
	
	public function renderForm()
	{
		return GDT_Template::php('UI', 'form/search.php', ['field' => $this]);
	}

}
