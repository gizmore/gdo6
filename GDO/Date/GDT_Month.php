<?php
namespace GDO\Date;

final class GDT_Month extends GDT_Date
{
	public function inputToVar($input)
	{
		$input = parent::inputToVar($input);
		$time = Time::parseDate($input);
		$input = Time::getDate($time, 'Y-m-01');
		return $input;
	}
	
}
