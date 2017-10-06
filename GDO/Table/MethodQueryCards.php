<?php
namespace GDO\Table;
abstract class MethodQueryCards extends MethodQueryList
{
	public function gdoListMode() { return GDT_List::MODE_CARD; }
}
