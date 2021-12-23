<?php
namespace GDO\Core;

/**
 * Callback GDT with additional paramters.
 * Renders by invoking it.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.10.3
 */
final class GDT_Callback extends GDT
{
    private $object;
    private $method;
    private $args;
    
    public function callback($object, $method, array $args=null)
    {
        $this->object = $object;
        $this->method = $method;
        $this->args = $args;
    }
    
    public function renderCell()
    {
        return call_user_func([$this->object, $this->method], ...$this->args);
    }
    
}
