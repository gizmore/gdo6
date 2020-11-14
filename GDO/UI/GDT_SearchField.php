<?php
namespace GDO\UI;

use GDO\DB\GDT_String;

/**
 * A search field is a text with icon and default label.
 * Input type is set to search.
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
class GDT_SearchField extends GDT_String
{
    public $hidden = true;
    public $orderable = false;
    public $filterable = false;
    public $searchable = false;
    
	public function defaultLabel() { return $this->label('search'); }

	public $_inputType = 'search';
	public $icon = 'search';
	
	public $min = 3;
	public $max = 128;

}
