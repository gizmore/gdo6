<?php
namespace GDO\Date;

/**
 * A Date select that only allows week start dates (mondays).
 * 
 * @author gizmore
 * @version 6.11.2
 */
final class GDT_Week extends GDT_Date
{
	public function _inputToVar($input)
	{
		$input = str_replace('T', ' ', $input);
		$input = str_replace('Z', '', $input);
		$time = Time::parseDate($input, Time::UTC);
		$monday = strtotime('last monday', $time + Time::ONE_DAY);
		$input = Time::getDate($monday, 'Y-m-d');
		return $input;
	}
	
}
