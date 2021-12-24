<?php
namespace GDO\Date;

/**
 * A date select that snaps to the beginning of a yearly quarter.
 * 
 * @author gizmore
 * @version 6.11.2
 */
final class GDT_Quarter extends GDT_Date
{
	public function _inputToVar($input)
	{
		$input = str_replace('T', ' ', $input);
		$input = str_replace('Z', '', $input);
		$time = Time::parseDate($input, Time::UTC);
		return self::getQuarterDate($time);
	}
	
	public static function getQuarterDate($time)
	{
		$month = self::getQuarterMonth(date('m'), $time);
		return date(sprintf('Y-%02d-01', $month), $time);
	}
	
	public static function getQuarterMonth($month)
	{
		$month = intval($month);
		$m = ($month - 1) % 3;
		return $month - $m;
	}
	
}
