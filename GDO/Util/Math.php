<?php
namespace GDO\Util;
/**
 * Math utility
 * @author gizmore
 * @since 1.0
 * @version 7.00
 */
class Math
{
	public static function clamp($number, $min=null, $max=null)
	{
		if ( ($min !== null) && ($number < $min) ) return $min;
		if ( ($max !== null) && ($number > $max) ) return $max;
		return $number;
	}
}
