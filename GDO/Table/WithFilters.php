<?php
namespace GDO\Table;
use GDO\Core\GDT_Fields;

trait WithFilters
{
    public $filters;
    public function filters(GDT_Fields $fields) { $this->filters = $fields; }
    
    
    
}