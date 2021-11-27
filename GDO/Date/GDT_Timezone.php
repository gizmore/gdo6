<?php
namespace GDO\Date;

use GDO\DB\GDT_ObjectSelect;

/**
 * Timezone select.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.10.0
 */
final class GDT_Timezone extends GDT_ObjectSelect
{
    public $max = 64;
    
    public function defaultName() { return 'timezone'; }
    public function defaultLabel() { return $this->label('timezone'); }
    
    protected function __construct()
    {
        parent::__construct();
        $this->notNull();
        $this->table = GDO_Timezone::table();
        $this->initial('1');
        $this->icon('time');
        $this->completionHref(href('Date', 'TimezoneComplete'));
        $this->searchable(false);
    }
    
    public function plugVar()
    {
        return GDO_Timezone::getBy('tz_name', 'Europe/Berlin');
    }

}
