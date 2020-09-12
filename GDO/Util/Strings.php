<?php
namespace GDO\Util;
/**
 * String utility class.
 * 
 * @author gizmore
 * @since 1.0
 * @version 6.11
 */
final class Strings
{
	######################
	### Start/End with ###
	######################
	public static function startsWith($s, $with)
	{
		return mb_strpos($s, $with) === 0;
	}
	
	public static function endsWith($s, $with)
	{
		if ($length = mb_strlen($with))
		{
			return mb_substr($s, -$length) === $with;
		}
		return true;
	}
	
	#########################
	### Substring to/from ###
	#########################
	public static function substrTo($s, $to, $default=null)
	{
		if (false !== ($index = mb_strpos($s, $to)))
		{
			return mb_substr($s, 0, $index);
		}
		return $default;
	}
	
	public static function substrFrom($s, $from, $default=null)
	{
		if (false !== ($index = mb_strpos($s, $from)))
		{
			return mb_substr($s, $index + mb_strlen($from));
		}
		return $default;
	}
	

	public static function rsubstrTo($s, $to, $default=null)
	{
		if (false !== ($index = mb_strrpos($s, $to)))
		{
			return mb_substr($s, 0, $index);
		}
		return $default;
	}
	
	public static function rsubstrFrom($s, $from, $default=null)
	{
		if (false !== ($index = mb_strrpos($s, $from)))
		{
			return mb_substr($s, $index + mb_strlen($from));
		}
		return $default;
	}

	#############
	### Chunk ###
	#############
	public static function chunkSplit($s, $length)
	{
	    return array_chunk(preg_split("//u", $s, -1, PREG_SPLIT_NO_EMPTY), $length);
	}

}
