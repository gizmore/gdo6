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
		$time = Time::parseDate($input, Time::UTC); # parse as if user meant UTC.
		$monday = strtotime('last monday', $time + Time::ONE_DAY); # php 5 fix?
		$input = Time::getDate($monday, 'Y-m-d'); # UTC DB date
		return $input;
	}
	
}
