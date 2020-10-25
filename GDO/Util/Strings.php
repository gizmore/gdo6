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
	
	#######################
	### HTML safe nl2br ###
	#######################
	/**
	 * Changes newline to <br/> but only when no tags are open.
	 * @param string $s
	 * @return string
	 */
	public static function nl2brHTMLSafe($s)
	{
	    $s = trim($s, " \r\n");
	    $len = strlen($s);
	    $open = 0;
	    $back = '';
	    for ($i = 0; $i < $len; $i++)
	    {
	        $c = $s[$i];
	        if ($c === '<')
	        {
	            $back .= $c;
	            $open++;
	        }
	        elseif ($c === '>')
	        {
	            $back .= $c;
	            $open--;
	        }
	        elseif ($c === "\r")
	        {
	            # skip
	        }
	        elseif ($c === "\n")
	        {
	            if (!$open)
	            {
	                $back .= "<br/>\n"; # safe to convert
	            }
	            else
	            {
	                $back .= ' '; # Open tag. use space instead.
	            }
	        }
	        else
	        {
	            $back .= $c;
	        }
	    }
	    return $back;
	}
	
}
