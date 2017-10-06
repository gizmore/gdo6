<?php
namespace GDO\Form;
use GDO\Core\GDT_Template;
use GDO\DB\GDT_String;
/**
 * A hidden form field.
 * @author gizmore
 * @version 7.00
 * @since 3.00
 */
class GDT_Hidden extends GDT_String
{
    public $readable = false;
    public $writable = false;
    public $editable = false;
    
    public function renderForm() { return GDT_Template::php('Form', 'form/hidden.php', ['field' => $this]); }
    public function renderCell() { return GDT_Template::php('Form', 'cell/hidden.php', ['field' => $this]); }
}
