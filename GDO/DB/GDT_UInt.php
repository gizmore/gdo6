<?php
namespace GDO\DB;
/**
 * Unsigned version of GDT_Int
 * @author gizmore
 * @version 6.10
 * @since 6.05
 */
class GDT_UInt extends GDT_Int
{
	public $unsigned = true;
	public $orderDefaultAsc = false;
}
