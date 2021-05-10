<?php
namespace GDO\Core;

/**
 * An array.
 * 
 * @see GDT_JSON
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.10.0
 */
class GDT_Array extends GDT
{
    public static function makeWith(array &$data)
    {
        return self::make()->data($data);
    }
    
    public function defaultName() { return 'data'; }
    
    public $data;
    public function data(array &$data)
    {
        $this->data = &$data;
        return $this;
    }

    public function renderCell() { return sprintf('<pre>%s</pre>', print_r($this->data, 1)); }
    public function renderForm() {}
    public function renderJSON() { return $this->data; }
    public function renderCLI() { return implode(', ', $this->data); }

}
