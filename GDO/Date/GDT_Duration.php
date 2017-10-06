<?php
namespace GDO\Date;
use GDO\DB\GDT_UInt;
/**
 * Duration field int in seconds.
 * @author gizmore
 * @version 7.00
 * @since 6.00
 */
class GDT_Duration extends GDT_UInt
{
	public function defaultLabel() { return $this->label('duration'); }
}
