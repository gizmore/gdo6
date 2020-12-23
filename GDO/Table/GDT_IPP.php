<?php
namespace GDO\Table;

use GDO\DB\GDT_UInt;

/**
 * Items per page for headers.
 * 
 * @author gizmore
 */
final class GDT_IPP extends GDT_UInt
{
    public $searchable = false;
    public $filterable = false;
    public $orderable = false;
    public $hidden = true;
    
    public $bytes = 2;
    
    public function defaultLabel() { return $this->label('ipp'); }
    
    protected function __construct()
    {
        $this->initial(Module_Table::instance()->cfgItemsPerPage());
    }
    
}
