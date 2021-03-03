<?php
namespace GDO\Core;

/**
 * Useful to wrap an array into an object.
 * No idea if i ever want to render this.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 * @see GDT_Select
 */
class GDT_Array extends GDT
{
    public $data;
    public function data(array &$data)
    {
        $this->data = &$data;
        return $this;
    }
    
}
