<?php
namespace GDO\Table;

use GDO\DB\GDT_UInt;

/**
 * Items per page for headers.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.1.0
 */
final class GDT_IPP extends GDT_UInt
{
    public $bytes = 2;
    public $hidden = true;
    public $orderable = false;
    public $searchable = false;
    public $filterable = false;
    public function isSerializable() { return false; }
    public function defaultLabel() { return $this->label('ipp'); }
    
    protected function __construct()
    {
        parent::__construct();
        $this->initial(Module_Table::instance()->cfgItemsPerPage());
        $this->min = 1;
        $this->max = 100;
    }
    
}
