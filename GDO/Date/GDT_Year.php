<?php
namespace GDO\Date;

/**
 * A date select that snaps to the beginning of a year.
 * 
 * @author gizmore
 * @version 6.11.2
 */
final class GDT_Year extends GDT_Date
{
	public function _inputToVar($input)
	{
		$input = str_replace('T', ' ', $input);
		$input = str_replace('Z', '', $input);
		$time = Time::parseDate($input, Time::UTC);
		$input = Time::getDate($time, 'Y-01-01');
		return $input;
	}
	
}
