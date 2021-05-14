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
    public $bytes = '2';
    public $initial = '1';
    
    public function defaultLabel() { return $this->label('page'); }

    public function isSerializable() { return false; }
    
}
