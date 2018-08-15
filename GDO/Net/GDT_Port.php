<?php
namespace GDO\Net;

use GDO\DB\GDT_Int;

final class GDT_Port extends GDT_Int
{
	public $min = 1;
	public $max = 65535;
	public $unsigned = true;
	public $bytes = 2;
}
