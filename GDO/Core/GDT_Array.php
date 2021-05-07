<?php
namespace GDO\Core;

/**
 * Useful to wrap an assoc array into an object.
 * Very similiar to GDT_JSON.
 * 
 * @TODO Is it more performant to use references?
 * @TODO maybe delete either GDT_JSON or GDT_Array. => NO! GDT_JSON does json codec transparently.
 * 
 * @see GDT_Select
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.10.0
 */
class GDT_Array extends GDT
{
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
