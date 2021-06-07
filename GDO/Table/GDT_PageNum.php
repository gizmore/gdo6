<?php
namespace GDO\Table;

use GDO\DB\GDT_UInt;

/**
 * Items per page for headers.
 * 
 * @author gizmore
 *
 */
final class GDT_PageNum extends GDT_UInt
{
    public $hidden = true;
    public $orderable = false;
    public $searchable = false;
    public $filterable = false;
    
    public $bytes = '2';
    public $initial = '1';
    
    public function defaultLabel() { return $this->label('page'); }

    public function isSerializable() { return false; }
    
    #############
    ### Table ###
    #############
    public $table;
    public function table(GDT_Table $table)
    {
        $this->table = $table;
        return $this;
    }

    ###############
    ### Example ###
    ###############
    public function gdoExampleVars()
    {
        $this->min = 1;
        $this->max = $this->table->getPageMenu()->getPageCount();
        return parent::gdoExampleVars();
    }

    ################
    ### Validate ###
    ################
    public function validate($value)
    {
        $this->min = 1;
        $this->max = $this->table->getPageMenu()->getPageCount();
        return parent::validate($value);
    }
    
}

