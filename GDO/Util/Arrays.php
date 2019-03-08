<?php
namespace GDO\Util;
/**
 * Array utility.
 * @author gizmore
 * @version 6.05
 */
final class Arrays
{
	public static function arrayed($value)
	{
		if (is_array($value))
		{
			return $value;
		}
		return $value === null ? [] : [$value];
	}
	
	/**
	 * Fixed explode with no elements on empty string.
	 * @param string $string
	 * @param string $delimiter
	 * @return array
	 */
	public static function explode($string, $delimiter=',')
	{
		return empty($string) ? array() : explode($delimiter, $string);
	}
	
	/**
	 * Recursive implode. Code taken from php.net.
	 * Original code by: kromped@yahoo.com
	 * @param string $glue
	 * @param array $pieces
	 * @return string
	 */
	public static function implode($glue, array $pieces, array $retVal=array())
	{
		foreach ($pieces as $r_pieces)
		{
			$retVal[] = is_array($r_pieces) ? '['.self::implode($glue, $r_pieces).']' : $r_pieces;
		}
		return implode($glue, $retVal);
	}
	
	/**
	 * Reverse an array but keep keys.
	 * @param array $array
	 * @return array
	 */
	public static function reverse(array $array)
	{
		$k = array_keys($array);
		$v = array_values($array);
		$rv = array_reverse($v);
		$rk = array_reverse($k);
		return array_combine($rk, $rv);
	}
}
