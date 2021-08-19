<?php
namespace GDO\Table;

/**
 * A method that displays a list.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
abstract class MethodList extends MethodTable
{
    public function createCollection() { $this->table = GDT_List::make(); return $this->table; }

}
