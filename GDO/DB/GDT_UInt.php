<?php
namespace GDO\DB;

/**
 * Unsigned version of GDT_Int
 * Sets a min value of 0.
 * Sets unsigned.
 * Sets default order direction to descending.
 * Base class of GDT_Object.

 * @author gizmore
 * @version 6.10.1
 * @since 6.5.0
 * 
 * @see GDT_Object
 */
class GDT_UInt extends GDT_Int
{
    public $min = 0;
    public $unsigned = true;
    public $orderDefaultAsc = false;

}
