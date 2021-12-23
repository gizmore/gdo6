<?php
namespace GDO\Date;

use GDO\DB\GDT_String;
use GDO\Core\GDT;
use GDO\DB\GDT_Char;

final class GDT_Month extends GDT_Date
{
// 	protected function __construct()
// 	{
// 		parent::__construct();
// 		$this->ascii();
// 		$this->caseS();
// 		$this->length(7);
// 	}
	
	public function inputToVar($input)
	{
		$input = parent::inputToVar($input);
		return $input;
	}
	
}
