<?php
namespace GDO\Table;

/**
 * Same stuff as list, just different templates.
 * @author gizmore
 */
abstract class MethodQueryCards extends MethodQueryList
{
	public function gdoListMode() { return GDT_List::MODE_CARD; }

}
