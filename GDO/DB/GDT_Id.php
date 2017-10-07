<?php
namespace GDO\DB;
class GDT_Id extends GDT_UInt
{
	public function defaultLabel() { return $this->label('id'); }
}
