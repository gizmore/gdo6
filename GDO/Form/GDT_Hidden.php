<?php
namespace GDO\Form;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_String;

/**
 * A hidden form field.
 * @author gizmore
 * @version 6.10.3
 * @since 3.0.0
 */
class GDT_Hidden extends GDT_String
{
    public $writable = true;
    public $editable = false;
    public $hidden = true;
	
	public function isSerializable() { return false; }
	
	public function renderForm() { return GDT_Template::php('Form', 'form/hidden.php', ['field' => $this]); }
	public function renderCell() { return GDT_Template::php('Form', 'cell/hidden.php', ['field' => $this]); }

}
