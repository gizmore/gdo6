<?php
namespace GDO\Date;

final class GDT_Week extends GDT_Date
{
	public function inputToVar($input)
	{
		$input = parent::inputToVar($input);
		$time = Time::parseDate($input);
		$monday = strtotime('last monday', $time + Time::ONE_DAY);
		$input = Time::getDate($monday, 'Y-m-d');
		return $input;
	}
	
}
