<?php
namespace GDO\Core;

/**
 * This GDT holds a method and executes it directly before rendering.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class GDT_Method extends GDT
{
    public $method;
    public function method(Method $method) { $this->method = $method; return $this; }
    
    public function renderCell()
    {
        echo $this->method->execute()->renderCell();
    }
    
    public function execute()
    {
        return  $this->method->execute();
    }
    
}
